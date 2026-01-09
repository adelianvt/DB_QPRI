<?php

namespace App\Http\Controllers;

use App\Models\Pengajuan;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApprovalController extends Controller
{
    /**
     * APPROVER: lihat list pengajuan yang butuh approval
     * Support filter:
     *  - ?search=
     *  - ?type=all|pengembangan|pengadaan
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $type   = $request->query('type', 'all');

        // status: submitted (yang masuk antrian approver)
        $submittedId = Status::where('code', 'submitted')->value('id');

        $query = Pengajuan::query()
            ->with(['status', 'maker'])
            ->where('status_id', $submittedId);

        // SEARCH (PostgreSQL: pakai ILIKE biar case-insensitive)
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'ILIKE', "%{$search}%")
                  ->orWhere('nomor_pengajuan', 'ILIKE', "%{$search}%");
            });
        }

        // TYPE filter (meta->tipe)
        if ($type !== 'all') {
            $query->where('meta->tipe', $type);
        }

        $pengajuans = $query->orderByDesc('id')->paginate(10)->withQueryString();

        return view('approval.index', compact('pengajuans'));
    }

    /**
     * Detail pengajuan
     */
    public function show(Pengajuan $pengajuan)
    {
        $pengajuan->load(['status', 'maker']);
        return view('approval.show', compact('pengajuan'));
    }

    /**
     * Approve pengajuan (POST)
     */
    public function approve(Request $request, Pengajuan $pengajuan)
    {
        $data = $request->validate([
            'approval_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $submittedId = Status::where('code', 'submitted')->value('id');
        abort_unless($pengajuan->status_id === $submittedId, 422, 'Hanya pengajuan submitted yang bisa di-approve');

        $approvedId = Status::where('code', 'approved')->value('id');

        $pengajuan->update([
            'status_id'       => $approvedId,
            'approved_by'     => Auth::id(),
            'approved_at'     => now(),
            'approval_notes'  => $data['approval_notes'] ?? null,
        ]);

        return redirect()->route('approvals.index')->with('success', 'Pengajuan berhasil di-approve');
    }

    /**
     * Reject pengajuan (POST)
     */
    public function reject(Request $request, Pengajuan $pengajuan)
    {
        $data = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:500'],
        ]);

        $submittedId = Status::where('code', 'submitted')->value('id');
        abort_unless($pengajuan->status_id === $submittedId, 422, 'Hanya pengajuan submitted yang bisa di-reject');

        $rejectedId = Status::where('code', 'rejected')->value('id');

        $pengajuan->update([
            'status_id'        => $rejectedId,
            'approved_by'      => Auth::id(),
            'approved_at'      => now(),
            'rejection_reason' => $data['rejection_reason'],
        ]);

        return redirect()->route('approvals.index')->with('success', 'Pengajuan berhasil di-reject');
    }

    
}
