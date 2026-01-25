<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pengajuan;
use App\Models\Status;
use Barryvdh\DomPDF\Facade\Pdf;

class PengajuanController extends Controller
{
    /* =====================================================
     * DASHBOARD
     * ===================================================== */
    public function dashboard(Request $r)
    {
        $baseQuery = $this->dataset($r);

        return view('dashboard', [
            'pengajuans' => (clone $baseQuery)->paginate(5)->withQueryString(),

            'approvedList' => (clone $baseQuery)
                ->whereHas('status', fn ($s) => $s->where('code', 'approved'))
                ->paginate(5, ['*'], 'approved_page')
                ->withQueryString(),

            'rejectedList' => (clone $baseQuery)
                ->whereHas('status', fn ($s) => $s->where('code', 'rejected'))
                ->paginate(5, ['*'], 'rejected_page')
                ->withQueryString(),

            'total'    => (clone $baseQuery)->count(),
            'approved' => (clone $baseQuery)->whereHas('status', fn ($s) => $s->where('code', 'approved'))->count(),
            'rejected' => (clone $baseQuery)->whereHas('status', fn ($s) => $s->where('code', 'rejected'))->count(),
            'waiting'  => (clone $baseQuery)->whereHas('status', fn ($s) => $s->where('code', 'like', 'pending%'))->count(),

            'type' => $r->type,
            'q'    => $r->q,
        ]);
    }

    /* =====================================================
     * HELPER
     * ===================================================== */
    private function statusId(string $code): int
    {
        return (int) Status::where('code', $code)->value('id');
    }

    private function statusCode(Pengajuan $p): string
    {
        return strtolower($p->status?->code ?? '');
    }

    private function roleLabel(): string
    {
        return match ((int) auth()->user()->role_id) {
            2  => 'GH CRV',
            3  => 'IAG',
            1  => 'CRV',
            14 => 'GH IAG',
            default => 'UNKNOWN',
        };
    }

    private function pushDecision(Pengajuan $p, string $action): void
    {
        $meta  = (array) ($p->meta ?? []);
        $steps = (array) data_get($meta, 'decision.steps', []);

        $steps[] = [
            'by_role' => $this->roleLabel(),
            'action'  => $action,
            'at'      => now()->toDateTimeString(),
        ];

        data_set($meta, 'decision.steps', $steps);
        $p->update(['meta' => $meta]);
    }

    /* =====================================================
     * DATASET (TIDAK DIUBAH)
     * ===================================================== */
    private function dataset(Request $r)
    {
        if ($r->get('type') === 'all') {
            $r->merge(['type' => null]);
        }

        $roleId = (int) Auth::user()->role_id;
        $q = Pengajuan::with(['status','maker'])->latest();

        if ($roleId === 3) {
            $q->where(function ($qq) {
                $qq->whereHas('status', fn ($s) =>
                    $s->whereIn('code', [
                        'pending_iag',
                        'pending_approver2',
                        'approved',
                    ])
                )->orWhere(function ($q2) {
                    $q2->whereHas('status', fn ($s) =>
                        $s->where('code', 'rejected')
                    )
                    ->whereRaw("
                        jsonb_path_exists(
                            (meta::jsonb)->'decision'->'steps',
                            '$[*] ? (@.by_role == \"GH IAG\" && @.action == \"reject\")'
                        )
                    ");
                });
            });
        }
        elseif ($roleId === 14) {
            $q->where(function ($qq) {
                $qq->whereHas('status', fn ($s) =>
                    $s->whereIn('code', [
                        'pending_approver2',
                        'approved',
                    ])
                )->orWhere(function ($q2) {
                    $q2->whereHas('status', fn ($s) =>
                        $s->where('code', 'rejected')
                    )
                    ->whereRaw("
                        jsonb_path_exists(
                            (meta::jsonb)->'decision'->'steps',
                            '$[*] ? (@.by_role == \"GH IAG\" && @.action == \"reject\")'
                        )
                    ");
                });
            });
        }
        else {
            $q->whereHas('status', fn ($s) =>
                $s->whereIn('code', [
                    'pending_approver1',
                    'pending_iag',
                    'pending_approver2',
                    'approved',
                    'rejected',
                ])
            );
        }

        if ($r->filled('q')) {
            $q->where('judul', 'ILIKE', '%'.$r->q.'%');
        }

        if ($r->filled('type')) {
            $q->where('meta->tipe', $r->type);
        }

        return $q;
    }

    /* =====================================================
     * CREATE / STORE
     * ===================================================== */
    public function create()
    {
        return view('pengajuan.create');
    }

    public function store(Request $r)
{
    $r->validate([
        // MAIN
        'judul' => 'required|string',
        'deskripsi' => 'required|string',
        'divisi' => 'required|string',
        'tipe' => 'required|string',

        // GROUP UTAMA
        'group_utama' => 'required|array|min:1',
        'group_utama.*' => 'required|string',

        'group_utama_2' => 'required|array|min:1',
        'group_utama_2.*' => 'required|string',

        // PENYUSUN
        'compiler_names' => 'required|array|min:1',
        'compiler_names.*' => 'required|string',

        // CONTACT PERSON (WATERMARK)
        'contact.nama' => 'required|string',
        'contact.hp' => 'required|string',
        'contact.email' => 'required|email',

        // RBB USERS
        'rbb_users.kode' => 'required|string',
        'rbb_users.nama' => 'required|string',
        'rbb_users.anggaran' => 'required|numeric',

        // RBB IT
        'rbb_it.kode' => 'required|string',
        'rbb_it.nama' => 'required|string',
        'rbb_it.bundling_anggaran' => 'required|numeric',
        'rbb_it.anggaran' => 'required|numeric',
    ]);

    Pengajuan::create([
        'judul'     => $r->judul,
        'deskripsi' => $r->deskripsi,
        'status_id' => $this->statusId('pending_approver1'),
        'maker_id'  => auth()->id(),
        'meta'      => $r->except('_token'),
    ]);

    return redirect()->route('dashboard');
}

    /* =====================================================
     * APPROVE (GH CRV & GH IAG)  ✅ FIX
     * ===================================================== */
    public function approve(Pengajuan $pengajuan)
    {
        $roleId = (int) auth()->user()->role_id;
        $code   = $this->statusCode($pengajuan);

        if ($roleId === 2 && $code === 'pending_approver1') {
            $next = 'pending_iag';
        }
        elseif ($roleId === 14 && $code === 'pending_approver2') {
            $next = 'approved';
        }
        else {
            abort(403);
        }

        $this->pushDecision($pengajuan, 'approve');

        $pengajuan->update([
            'status_id' => $this->statusId($next),
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back();
    }

    /* =====================================================
     * REJECT (GH CRV & GH IAG)  ✅ FIX
     * ===================================================== */
    public function reject(Request $r, Pengajuan $pengajuan)
    {
        $r->validate([
            'rejection_reason' => 'required|string',
        ]);

        if (!in_array((int) auth()->user()->role_id, [2,14])) {
            abort(403);
        }

        $this->pushDecision($pengajuan, 'reject');

        $pengajuan->update([
            'status_id' => $this->statusId('rejected'),
            'rejection_reason' => $r->rejection_reason,
        ]);

        return back();
    }
/* =====================================================
 * INDEX (WAJIB ADA KARENA ROUTE PAKAI INI)
 * ===================================================== */
public function index(Request $r)
{
    $baseQuery = $this->dataset($r);

    return view('pengajuan.index', [
        'pengajuans' => (clone $baseQuery)->paginate(10)->withQueryString(),

        'total'    => (clone $baseQuery)->count(),
        'approved' => (clone $baseQuery)
            ->whereHas('status', fn ($s) => $s->where('code', 'approved'))
            ->count(),

        'rejected' => (clone $baseQuery)
            ->whereHas('status', fn ($s) => $s->where('code', 'rejected'))
            ->count(),

        'waiting'  => (clone $baseQuery)
            ->whereHas('status', fn ($s) => $s->where('code', 'like', 'pending%'))
            ->count(),

        'q'    => $r->q,
        'type' => $r->type,
    ]);
}
/* =====================================================
 * EDIT (WAJIB – BIAR ROUTE /pengajuans/{id}/edit TIDAK ERROR)
 * ===================================================== */
public function edit(Pengajuan $pengajuan)
{
    $roleId = (int) auth()->user()->role_id;
    $status = strtolower($pengajuan->status?->code ?? '');

    // ✅ CRV boleh edit jika REJECTED (oleh siapapun)
    if ($roleId === 1 && $status === 'rejected') {
        return view('pengajuan.edit', compact('pengajuan'));
    }

    // IAG boleh edit saat proses IAG
    if ($roleId === 3 && in_array($status, ['pending_iag', 'pending_approver2'])) {
        return view('pengajuan.iag_edit', compact('pengajuan'));
    }

    abort(403, 'Anda tidak memiliki akses edit pengajuan ini');
}

public function update(Request $r, Pengajuan $pengajuan)
{
    if ((int) auth()->user()->role_id !== 1) {
        abort(403);
    }

    if ($this->statusCode($pengajuan) !== 'rejected') {
        abort(403);
    }

    $r->validate([
        'judul' => 'required|string',
        'deskripsi' => 'required|string',
        'divisi' => 'required|string',
        'tipe' => 'required|string',

        'group_utama' => 'required|array|min:1',
        'group_utama.*' => 'required|string',

        'group_utama_2' => 'required|array|min:1',
        'group_utama_2.*' => 'required|string',

        'compiler_names' => 'required|array|min:1',
        'compiler_names.*' => 'required|string',

        'contact.nama' => 'required|string',
        'contact.hp' => 'required|string',
        'contact.email' => 'required|email',

        'rbb_users.kode' => 'required|string',
        'rbb_users.nama' => 'required|string',
        'rbb_users.anggaran' => 'required|numeric',

        'rbb_it.kode' => 'required|string',
        'rbb_it.nama' => 'required|string',
        'rbb_it.bundling_anggaran' => 'required|numeric',
        'rbb_it.anggaran' => 'required|numeric',
    ]);

    $meta = (array) ($pengajuan->meta ?? []);
    $meta = array_replace_recursive($meta, $r->except('_token','_method'));

    $pengajuan->update([
        'judul' => $r->judul,
        'deskripsi' => $r->deskripsi,
        'meta' => $meta,

        // BALIK KE AWAL
        'status_id' => $this->statusId('pending_approver1'),
        'rejection_reason' => null,
    ]);

    return redirect()->route('dashboard');
}

/* =====================================================
 * UPDATE IAG (WAJIB – BIAR /pengajuans/{id}/iag TIDAK ERROR)
 * ===================================================== */
public function iagUpdate(Request $r, Pengajuan $pengajuan)
{
    if ((int) auth()->user()->role_id !== 3) {
        abort(403);
    }

    $r->validate([
        'iag.kode_project' => 'required',
        'iag.nama_project' => 'required',
        'iag.itag_list' => 'required|array',
        'iag.itw_list' => 'required|array',
        'iag.karakter' => 'required|array',
    ]);

    // ambil meta lama (CONTACT PERSON TETAP AMAN)
    $meta = (array) ($pengajuan->meta ?? []);
    data_set($meta, 'iag', $r->iag);

    $pengajuan->update([
        'meta' => $meta,
        'status_id' => $this->statusId('pending_approver2'),
    ]);

    // catat decision
    $this->pushDecision($pengajuan, 'submit_iag');

    return redirect()->route('pengajuans.show', $pengajuan->id);
}
    /* =====================================================
     * SHOW / DOWNLOAD
     * ===================================================== */
    public function show(Pengajuan $pengajuan)
    {
        return view('pengajuan.show', compact('pengajuan'));
    }

    public function download(Pengajuan $pengajuan)
    {
        return Pdf::loadView('pengajuan.pdf', compact('pengajuan'))
            ->download('QPRI-'.$pengajuan->id.'.pdf');
    }
/* =====================================================
 * DESTROY (WAJIB – BIAR DELETE TIDAK ERROR)
 * ===================================================== */
public function destroy(Pengajuan $pengajuan)
{
    $roleId = (int) auth()->user()->role_id;
    $status = $this->statusCode($pengajuan);

    // ✅ CRV & IAG BOLEH DELETE (SESUSAI REQUIREMENT KAMU)
    if (!in_array($roleId, [3, 1])) {
        abort(403);
    }

    // ❌ TIDAK BOLEH DELETE kalau sudah approved
    if ($status === 'approved') {
        return back()->with('error', 'Pengajuan sudah approved, tidak bisa dihapus');
    }

    // ✅ SEMUA REJECTED BOLEH DIHAPUS
    $pengajuan->delete();

    return redirect()
        ->route('pengajuans.index')
        ->with('success', 'Pengajuan berhasil dihapus');
}
}