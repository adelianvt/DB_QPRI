<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Pengajuan;
use App\Models\Status;

use Barryvdh\DomPDF\Facade\Pdf;
use ZipArchive;

class PengajuanController extends Controller
{
    /* =========================
     * HELPER
     * ========================= */
    private function statusId(string $code): int
    {
        return (int) Status::where('code', $code)->value('id');
    }

    private function pushDecisionStep(Pengajuan $p, string $byRole, string $action): void
    {
        $meta  = (array) ($p->meta ?? []);
        $steps = (array) data_get($meta, 'decision.steps', []);

        $steps[] = [
            'by_role' => $byRole,
            'action'  => $action,
            'at'      => now()->toDateTimeString(),
        ];

        data_set($meta, 'decision.steps', $steps);
        $p->meta = $meta;
    }

    private function statusCode(?Pengajuan $p): string
    {
        return strtolower($p?->status?->code ?? '');
    }

    /**
     * Ambil actor terakhir dari meta decision steps (GH CRV / IAG / GH IAG / CRV)
     */
    private function lastActor(?Pengajuan $p): ?string
    {
        $steps = data_get($p?->meta, 'decision.steps', []);
        if (!is_array($steps) || !count($steps)) return null;

        $last = $steps[count($steps) - 1] ?? null;
        if (!is_array($last)) return null;

        $actor = $last['by_role'] ?? null;
        if (!$actor) return null;

        // normalisasi biar konsisten
        $a = strtoupper(trim((string)$actor));
        if (str_contains($a, 'GH') && str_contains($a, 'CRV')) return 'GH CRV';
        if ($a === 'CRV') return 'CRV';
        if ($a === 'IAG') return 'IAG';
        if (str_contains($a, 'GH') && str_contains($a, 'IAG')) return 'GH IAG';

        return strtoupper((string)$actor);
    }

    /**
     * ✅ APPLY VISIBILITY PER ROLE
     * - role sebelumnya tetap melihat data walau sudah lewat tahap berikutnya
     * - rejected GH CRV tidak “bablas” ke IAG/GH IAG
     */
    private function applyVisibilityByRole($query, int $roleId)
    {
        // CRV (1) & GH CRV (2): lihat SEMUA (biar history tidak hilang)
        if ($roleId === 1 || $roleId === 2) {
            return $query;
        }

        // IAG (3): hanya yang sudah masuk tahap IAG / lanjutannya
        // - pending_iag (boleh isi)
        // - pending_approver2 (sudah disubmit IAG)
        // - approved (selesai)
        // - rejected: tampil kalau reject-nya BUKAN GH CRV
        if ($roleId === 3) {
            return $query->where(function ($w) {
                $w->whereHas('status', fn($s) => $s->whereIn('code', [
                        'pending_iag',
                        'pending_approver2',
                        'approved',
                    ]))
                  ->orWhere(function ($w2) {
                      $w2->whereHas('status', fn($s) => $s->where('code', 'rejected'))
                         ->whereRaw("UPPER(COALESCE(meta->'decision'->'steps'->-1->>'by_role','')) <> 'GH CRV'");
                  });
            });
        }

        // GH IAG (14): hanya yang sudah masuk tahap GH IAG / selesai
        // - pending_approver2
        // - approved
        // - rejected: hanya kalau rejected by GH IAG
        if ($roleId === 14) {
            return $query->where(function ($w) {
                $w->whereHas('status', fn($s) => $s->whereIn('code', [
                        'pending_approver2',
                        'approved',
                    ]))
                  ->orWhere(function ($w2) {
                      $w2->whereHas('status', fn($s) => $s->where('code', 'rejected'))
                         ->whereRaw("UPPER(COALESCE(meta->'decision'->'steps'->-1->>'by_role','')) = 'GH IAG'");
                  });
            });
        }

        // default role lain: lihat semua
        return $query;
    }

    /* =========================
     * DASHBOARD
     * ========================= */
    public function dashboard(Request $request)
    {
        $q    = $request->get('q');
        $type = $request->get('type', 'all');
        $roleId = (int) (Auth::user()?->role_id ?? 0);

        $query = Pengajuan::query()->with(['maker', 'status'])->latest();

        if ($q) {
            $query->where(function ($w) use ($q) {
                $w->where('judul', 'ilike', "%{$q}%")
                  ->orWhere('deskripsi', 'ilike', "%{$q}%");
            });
        }

        if ($type && $type !== 'all') {
            $query->whereRaw("LOWER(COALESCE(meta->>'tipe','')) = ?", [strtolower($type)]);
        }

        $query = $this->applyVisibilityByRole($query, $roleId);

        $total    = (clone $query)->count();
        $approved = (clone $query)->whereHas('status', fn($s)=>$s->where('code','approved'))->count();
        $rejected = (clone $query)->whereHas('status', fn($s)=>$s->where('code','rejected'))->count();
        $waiting  = (clone $query)->whereHas('status', fn($s)=>$s->where('code','like','pending%'))->count();

        $approvedList = (clone $query)->whereHas('status', fn($s)=>$s->where('code','approved'))->take(10)->get();
        $rejectedList = (clone $query)->whereHas('status', fn($s)=>$s->where('code','rejected'))->take(10)->get();

        return view('dashboard', compact(
            'q','type',
            'total','approved','waiting','rejected',
            'approvedList','rejectedList'
        ));
    }

    /* =========================
     * INDEX (Management)
     * ========================= */
    public function index(Request $request)
    {
        $q    = $request->get('q');
        $type = $request->get('type', 'all');
        $roleId = (int) (Auth::user()?->role_id ?? 0);

        $query = Pengajuan::query()->with(['maker', 'status'])->latest();

        if ($q) {
            $query->where(function ($w) use ($q) {
                $w->where('judul', 'ilike', "%{$q}%")
                  ->orWhere('deskripsi', 'ilike', "%{$q}%");
            });
        }

        if ($type && $type !== 'all') {
            $query->whereRaw("LOWER(COALESCE(meta->>'tipe','')) = ?", [strtolower($type)]);
        }

        // ✅ VISIBILITY FIX UTAMA ADA DI SINI
        $query = $this->applyVisibilityByRole($query, $roleId);

        $pengajuans = $query->paginate(10)->withQueryString();

        // ✅ total card di management harus sama “dataset yang tampil”
        $total    = (clone $query)->count();
        $approved = (clone $query)->whereHas('status', fn($s)=>$s->where('code','approved'))->count();
        $rejected = (clone $query)->whereHas('status', fn($s)=>$s->where('code','rejected'))->count();
        $waiting  = (clone $query)->whereHas('status', fn($s)=>$s->where('code','like','pending%'))->count();

        return view('pengajuan.index', compact(
            'pengajuans','q','type',
            'total','approved','rejected','waiting'
        ));
    }

    /* =========================
     * CREATE / EDIT / SHOW
     * ========================= */
    public function create()
    {
        return view('pengajuan.create');
    }

    public function edit(Pengajuan $pengajuan)
    {
        return view('pengajuan.edit', compact('pengajuan'));
    }

    public function show(Pengajuan $pengajuan)
    {
        return view('pengajuan.show', compact('pengajuan'));
    }

    /* =========================
     * CRV SUBMIT
     * ========================= */
    public function store(Request $request)
    {
        $request->validate([
            'judul'     => 'required|string|max:255',
            'deskripsi' => 'required|string',
        ]);

        $p = new Pengajuan();
        $p->judul     = $request->judul;
        $p->deskripsi = $request->deskripsi;
        $p->maker_id  = Auth::id();

        $p->meta = $request->except(['_token']);

        // status awal -> GH CRV
        $p->status_id = $this->statusId('pending_approver1');
        $p->rejection_reason = null;

        $this->pushDecisionStep($p, 'CRV', 'submit');
        $p->save();

        return redirect()->route('pengajuans.index');
    }

    /* =========================
     * CRV UPDATE
     * ========================= */
    public function update(Request $request, Pengajuan $pengajuan)
    {
        $request->validate([
            'judul'     => 'required|string|max:255',
            'deskripsi' => 'required|string',
        ]);

        $pengajuan->judul     = $request->judul;
        $pengajuan->deskripsi = $request->deskripsi;

        $metaOld  = (array) ($pengajuan->meta ?? []);
        $incoming = $request->except(['_token', '_method']);
        $metaNew  = array_merge($metaOld, $incoming);
        $pengajuan->meta = $metaNew;

        $code = $this->statusCode($pengajuan);

        if ($code === 'rejected') {
            // resubmit balik ke GH CRV
            $pengajuan->status_id = $this->statusId('pending_approver1');
            $pengajuan->rejection_reason = null;
            $this->pushDecisionStep($pengajuan, 'CRV', 'resubmit_after_reject');
        } else {
            $this->pushDecisionStep($pengajuan, 'CRV', 'update');
        }

        $pengajuan->save();
        return redirect()->route('pengajuans.index');
    }

    /* =========================
     * DESTROY (biar delete gak error)
     * ========================= */
    public function destroy(Pengajuan $pengajuan)
    {
        $roleId = (int) (Auth::user()?->role_id ?? 0);
        $code   = $this->statusCode($pengajuan);

        // sesuai aturan view kamu:
        // - CRV boleh delete kalau rejected
        if ($roleId === 1) {
            if ($code !== 'rejected') abort(403, 'Hanya bisa delete saat status rejected.');
            $this->pushDecisionStep($pengajuan, 'CRV', 'delete');
            $pengajuan->save();
            $pengajuan->delete();

            return redirect()->route('pengajuans.index')->with('success', 'Data berhasil dihapus.');
        }

        // - IAG kamu juga kasih delete di view
        if ($roleId === 3) {
            $this->pushDecisionStep($pengajuan, 'IAG', 'delete');
            $pengajuan->save();
            $pengajuan->delete();

            return redirect()->route('pengajuans.index')->with('success', 'Data berhasil dihapus.');
        }

        abort(403, 'Tidak punya akses delete.');
    }

    /* =========================
     * APPROVE
     * ========================= */
    public function approve(Pengajuan $pengajuan)
    {
        $roleId = (int) (Auth::user()?->role_id ?? 0);
        $code   = $this->statusCode($pengajuan);

        // GH CRV approve: pending_approver1 -> pending_iag
        if ($roleId === 2 && $code === 'pending_approver1') {
            $pengajuan->status_id = $this->statusId('pending_iag');
            $this->pushDecisionStep($pengajuan, 'GH CRV', 'approve');
            $pengajuan->save();
            return redirect()->route('pengajuans.index');
        }

        // GH IAG approve: pending_approver2 -> approved
        if ($roleId === 14 && $code === 'pending_approver2') {
            $pengajuan->status_id = $this->statusId('approved');
            $this->pushDecisionStep($pengajuan, 'GH IAG', 'approve');
            $pengajuan->save();
            return redirect()->route('pengajuans.index');
        }

        abort(403, 'Tidak sesuai tahap approval.');
    }

    /* =========================
     * REJECT
     * ========================= */
    public function reject(Request $request, Pengajuan $pengajuan)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $roleId = (int) (Auth::user()?->role_id ?? 0);
        $code   = $this->statusCode($pengajuan);

        // GH CRV reject: STOP DI SINI (status rejected)
        if ($roleId === 2 && $code === 'pending_approver1') {
            $pengajuan->status_id = $this->statusId('rejected');
            $pengajuan->rejection_reason = $request->rejection_reason;
            $this->pushDecisionStep($pengajuan, 'GH CRV', 'reject');
            $pengajuan->save();
            return redirect()->route('pengajuans.index');
        }

        // GH IAG reject: status rejected
        if ($roleId === 14 && $code === 'pending_approver2') {
            $pengajuan->status_id = $this->statusId('rejected');
            $pengajuan->rejection_reason = $request->rejection_reason;
            $this->pushDecisionStep($pengajuan, 'GH IAG', 'reject');
            $pengajuan->save();
            return redirect()->route('pengajuans.index');
        }

        abort(403, 'Tidak sesuai tahap reject.');
    }

    /* =========================
     * IAG FORM
     * ========================= */
    public function iagEdit(Pengajuan $pengajuan)
    {
        $roleId = (int) (Auth::user()?->role_id ?? 0);
        $code   = $this->statusCode($pengajuan);

        if ($roleId !== 3) abort(403, 'Hanya role IAG.');
        if ($code !== 'pending_iag') abort(403, 'Tahap belum sampai IAG.');

        return view('pengajuan.iag_edit', [
            'pengajuan' => $pengajuan,
            'meta'      => (array) ($pengajuan->meta ?? []),
        ]);
    }

    public function iagUpdate(Request $request, Pengajuan $pengajuan)
    {
        $roleId = (int) (Auth::user()?->role_id ?? 0);
        $code   = $this->statusCode($pengajuan);

        if ($roleId !== 3) abort(403, 'Hanya role IAG.');
        if ($code !== 'pending_iag') abort(403, 'Tahap belum sampai IAG.');

        $request->validate([
            'iag.kode_project' => 'required|string|max:100',
            'iag.nama_project' => 'required|string|max:255',
            'iag.itag_list'    => 'required|array|min:1',
            'iag.itw_list'     => 'required|array|min:1',
            'iag.karakter'     => 'required|array|min:1',
        ]);

        $meta = (array) ($pengajuan->meta ?? []);

        data_set($meta, 'iag.kode_project', $request->input('iag.kode_project'));
        data_set($meta, 'iag.nama_project', $request->input('iag.nama_project'));
        data_set($meta, 'iag.it_arch_governance', $request->input('iag.itag_list', []));
        data_set($meta, 'iag.it_technical_writer', $request->input('iag.itw_list', []));
        data_set($meta, 'iag.karakteristik', $request->input('iag.karakter', []));

        $pengajuan->meta = $meta;

        // setelah IAG submit -> nunggu GH IAG
        $pengajuan->status_id = $this->statusId('pending_approver2');

        $this->pushDecisionStep($pengajuan, 'IAG', 'submit_iag_form');
        $pengajuan->save();

        return redirect()->route('pengajuans.index');
    }

    /* =========================
     * DOWNLOAD SINGLE (PDF)
     * ========================= */
    public function download(Pengajuan $pengajuan)
    {
        $p = $pengajuan;
        $meta = (array) ($p->meta ?? []);

        $pdf = Pdf::loadView('pengajuan.pdf', compact('p', 'meta'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('pengajuan-' . $p->id . '.pdf');
    }

    /* =========================
     * DOWNLOAD SELECTED (ZIP isi PDF)
     * ========================= */
    public function downloadSelected(Request $request)
    {
        $ids = (array) $request->input('ids', []);
        $ids = array_values(array_filter($ids));

        if (!count($ids)) {
            return back()->with('error', 'Pilih minimal 1 data dulu.');
        }

        $items = Pengajuan::with(['maker', 'status'])->whereIn('id', $ids)->get();

        $zipName = 'pengajuan-selected-' . now()->format('YmdHis') . '.zip';
        $zipPath = storage_path('app/' . $zipName);

        $zip = new ZipArchive();
        $ok = $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        if ($ok !== true) {
            abort(500, 'Gagal membuat file ZIP.');
        }

        foreach ($items as $p) {
            $meta = (array) ($p->meta ?? []);
            $pdf = Pdf::loadView('pengajuan.pdf', compact('p', 'meta'))
                ->setPaper('a4', 'portrait');

            $zip->addFromString('pengajuan-' . $p->id . '.pdf', $pdf->output());
        }

        $zip->close();

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }
}