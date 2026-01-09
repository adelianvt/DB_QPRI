<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pengajuan;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PengajuanApiController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $q = Pengajuan::query()->with(['status','maker'])->orderByDesc('id');

        if ($user->hasRole('maker')) {
            $q->where('maker_id', $user->id);
        } elseif ($user->hasRole('admin')) {
            $statusIds = Status::whereIn('code', ['submitted','in_review','returned','pending_approval'])->pluck('id');
            $q->whereIn('status_id', $statusIds);
        } elseif ($user->hasRole('approver')) {
            $pendingId = Status::where('code','pending_approval')->value('id');
            $q->where('status_id', $pendingId);
        } else {
            return response()->json(['message' => 'Role not allowed'], 403);
        }

        return response()->json($q->get());
    }

    public function store(Request $request)
    {
        $user = $request->user();
        if (!$user->hasRole('maker')) {
            return response()->json(['message' => 'Only maker can create'], 403);
        }

        $data = $request->validate([
            'judul' => ['required','string','max:255'],
            'deskripsi' => ['required','string'],
            'estimasi_biaya' => ['nullable','numeric','min:0'],
            'tanggal_mulai' => ['nullable','date'],
            'tanggal_selesai' => ['nullable','date','after_or_equal:tanggal_mulai'],
        ]);

        $draftId = Status::where('code','draft')->value('id');
        if (!$draftId) return response()->json(['message' => 'Status draft not found'], 500);

        $nomor = 'PJ-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));

        $p = Pengajuan::create([
            'nomor_pengajuan' => $nomor,
            'maker_id' => $user->id,
            'status_id' => $draftId,
            'judul' => $data['judul'],
            'deskripsi' => $data['deskripsi'],
            'estimasi_biaya' => $data['estimasi_biaya'] ?? null,
            'tanggal_mulai' => $data['tanggal_mulai'] ?? null,
            'tanggal_selesai' => $data['tanggal_selesai'] ?? null,
        ]);

        return response()->json($p->load('status'), 201);
    }

    public function submit(Request $request, Pengajuan $pengajuan)
    {
        $user = $request->user();
        if (!$user->hasRole('maker')) return response()->json(['message' => 'Only maker can submit'], 403);
        if ($pengajuan->maker_id !== $user->id) return response()->json(['message' => 'Forbidden'], 403);

        $draftId = Status::where('code','draft')->value('id');
        $submittedId = Status::where('code','submitted')->value('id');

        if ($pengajuan->status_id !== $draftId) {
            return response()->json(['message' => 'Only draft can be submitted'], 422);
        }

        $pengajuan->update([
            'status_id' => $submittedId,
            'submitted_at' => now(),
        ]);

        return response()->json($pengajuan->fresh()->load('status'));
    }

    public function markInReview(Request $request, Pengajuan $pengajuan)
    {
        $user = $request->user();
        if (!$user->hasRole('admin')) return response()->json(['message' => 'Only admin'], 403);

        $inReviewId = Status::where('code','in_review')->value('id');
        $pengajuan->update(['status_id' => $inReviewId]);

        return response()->json($pengajuan->fresh()->load('status'));
    }

    public function returnToMaker(Request $request, Pengajuan $pengajuan)
    {
        $user = $request->user();
        if (!$user->hasRole('admin')) return response()->json(['message' => 'Only admin'], 403);

        $returnedId = Status::where('code','returned')->value('id');
        $pengajuan->update(['status_id' => $returnedId]);

        return response()->json($pengajuan->fresh()->load('status'));
    }

    public function forwardToApprover(Request $request, Pengajuan $pengajuan)
    {
        $user = $request->user();
        if (!$user->hasRole('admin')) return response()->json(['message' => 'Only admin'], 403);

        $pendingId = Status::where('code','pending_approval')->value('id');
        $pengajuan->update(['status_id' => $pendingId]);

        return response()->json($pengajuan->fresh()->load('status'));
    }

    public function approve(Request $request, Pengajuan $pengajuan)
    {
        $user = $request->user();
        if (!$user->hasRole('approver')) return response()->json(['message' => 'Only approver'], 403);

        $approvedId = Status::where('code','approved')->value('id');

        $pengajuan->update([
            'status_id' => $approvedId,
            'approved_at' => now(),
        ]);

        return response()->json($pengajuan->fresh()->load('status'));
    }

    public function reject(Request $request, Pengajuan $pengajuan)
    {
        $user = $request->user();
        if (!$user->hasRole('approver')) return response()->json(['message' => 'Only approver'], 403);

        $rejectedId = Status::where('code','rejected')->value('id');

        $pengajuan->update([
            'status_id' => $rejectedId,
            'approved_at' => now(),
        ]);

        return response()->json($pengajuan->fresh()->load('status'));
    }
}
