@extends('layouts.app')

@section('content')

@php
  $user = auth()->user();
  $role = (int) ($user?->role_id ?? 0);

  $statusCode = fn($p) => strtolower($p->status?->code ?? '');

  $statusText = function($p){
      $code  = strtolower($p->status?->code ?? '');
      $steps = data_get($p->meta, 'decision.steps', []);
      $actor = null;

      if (is_array($steps) && count($steps)) {
        $last = $steps[count($steps)-1] ?? null;
        if (is_array($last)) $actor = $last['by_role'] ?? null;
      }

      $actorN = $actor ? strtoupper(trim((string)$actor)) : null;
      if ($actorN) {
        if (str_contains($actorN, 'GH') && str_contains($actorN, 'CRV')) $actorN = 'GH CRV';
        if ($actorN === 'CRV') $actorN = 'CRV';
        if ($actorN === 'IAG') $actorN = 'IAG';
        if (str_contains($actorN, 'GH') && str_contains($actorN, 'IAG')) $actorN = 'GH IAG';
      }

      return match($code){
        'pending_approver1' => 'Waiting by GH CRV',
        'pending_iag'       => 'Waiting by IAG',
        'pending_approver2' => 'Waiting by GH IAG',
        'approved'          => $actorN ? "Approved by {$actorN}" : 'Approved',
        'rejected'          => $actorN ? "Rejected by {$actorN}" : 'Rejected',
        default             => 'Waiting For Approval',
      };
  };

  $badgeClass = fn($p) => match(strtolower($p->status?->code ?? '')){
      'approved' => 'border-green-600 text-green-600 bg-green-50',
      'rejected' => 'border-red-600 text-red-600 bg-red-50',
      'pending_iag' => 'border-blue-600 text-blue-600 bg-blue-50',
      default    => 'border-yellow-600 text-yellow-600 bg-yellow-50',
  };

  $typeLabel = fn($v) => match(strtolower((string)$v)){
      'internal' => 'Pengembangan',
      'pengadaan' => 'Pengadaan',
      default => '-',
  };

  // ⬇️ ACTION RULE — ASLI, TIDAK DIUBAH
  $allowedActions = function($role, $p) use ($statusCode){
    $code  = $statusCode($p);
    $steps = data_get($p->meta, 'decision.steps', []);

    // 🔥 cek apakah pernah di-reject oleh GH CRV
    $rejectedByGhCrv = false;
    foreach ((array)$steps as $st) {
        if (
            ($st['action'] ?? '') === 'reject' &&
            ($st['by_role'] ?? '') === 'GH CRV'
        ) {
            $rejectedByGhCrv = true;
            break;
        }
    }

    // APPROVAL ROLE (GH)
    if ($role === 2 || $role === 14) {
        return ['view'];
    }

    // ✅ CRV / MAKER
    if ($role === 1) {
        if ($code === 'rejected' || $rejectedByGhCrv) {
            return ['view','edit','delete'];
        }
        return ['view'];
    }

    // IAG
    if ($role === 3) {
        if (in_array($code, ['approved','rejected'])) {
            return ['view','delete'];
        }
        return ['view','edit','delete'];
    }

    return ['view'];
};
@endphp

{{-- ================= STYLE (SAMA DASHBOARD) ================= --}}
<style>
  @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap');
  .qpri-font { font-family: 'Inter', sans-serif; }
  .hover-lift { transition: transform 200ms ease-in, background-color 200ms ease-in; }
  .hover-lift:hover { transform: translateY(-2px); }
</style>

<div class="qpri-font">

{{-- ================= FILTER (AMBIL DARI KODE 1) ================= --}}
<form method="GET" action="{{ url()->current() }}">
  <div class="bg-white border border-gray-200 rounded-lg p-6 mb-6">
    <div class="flex items-center justify-between gap-6">

      <div class="flex-1 max-w-md">
        <div class="relative">
          <input type="search" name="q" value="{{ $q ?? '' }}" placeholder="Search"
            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"
               width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"
               viewBox="0 0 24 24">
            <circle cx="11" cy="11" r="8"></circle>
            <path d="m21 21-4.35-4.35"></path>
          </svg>
        </div>
      </div>

      <div class="relative w-52">
        <select name="type"
          class="w-full pl-4 pr-10 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-indigo-500"
          onchange="this.form.submit()">
          <option value="all" {{ ($type ?? 'all') === 'all' ? 'selected' : '' }}>Type : All</option>
          <option value="internal" {{ ($type ?? '') === 'internal' ? 'selected' : '' }}>Type : Pengembangan</option>
          <option value="pengadaan" {{ ($type ?? '') === 'pengadaan' ? 'selected' : '' }}>Type : Pengadaan</option>
        </select>
      </div>

    </div>
  </div>
</form>

{{-- ================= SUMMARY ================= --}}
{{-- SUMMARY CARDS --}}
  <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

    <div class="bg-white rounded-lg p-6 border border-gray-200 hover-lift">
      <div class="flex items-center gap-4">
        <div class="w-12 h-12 rounded-lg bg-indigo-100 flex items-center justify-center">
          <svg class="text-indigo-600" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
            <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path>
          </svg>
        </div>
        <div>
          <p class="text-3xl font-semibold text-indigo-600">{{ $total ?? 0 }}</p>
          <p class="text-sm text-gray-600">Total Project</p>
        </div>
      </div>
    </div>

    <div class="bg-white rounded-lg p-6 border border-gray-200 hover-lift">
      <div class="flex items-center gap-4">
        <div class="w-12 h-12 rounded-lg bg-green-100 flex items-center justify-center">
          <svg class="text-green-600" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
            <polyline points="20 6 9 17 4 12"></polyline>
          </svg>
        </div>
        <div>
          <p class="text-3xl font-semibold text-green-600">{{ $approved ?? 0 }}</p>
          <p class="text-sm text-gray-600">Approved</p>
        </div>
      </div>
    </div>

    <div class="bg-white rounded-lg p-6 border border-gray-200 hover-lift">
      <div class="flex items-center gap-4">
        <div class="w-12 h-12 rounded-lg bg-yellow-100 flex items-center justify-center">
          <svg class="text-yellow-600" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10"></circle>
            <polyline points="12 6 12 12 16 14"></polyline>
          </svg>
        </div>
        <div>
          <p class="text-3xl font-semibold text-yellow-600">{{ $waiting ?? 0 }}</p>
          <p class="text-sm text-gray-600">Waiting For Approval</p>
        </div>
      </div>
    </div>

    <div class="bg-white rounded-lg p-6 border border-gray-200 hover-lift">
      <div class="flex items-center gap-4">
        <div class="w-12 h-12 rounded-lg bg-red-100 flex items-center justify-center">
          <svg class="text-red-600" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </div>
        <div>
          <p class="text-3xl font-semibold text-red-600">{{ $rejected ?? 0 }}</p>
          <p class="text-sm text-gray-600">Rejected</p>
        </div>
      </div>
    </div>
  </section>


{{-- ================= TABLE (AMBIL DARI KODE 2) ================= --}}
<section class="bg-white rounded-lg overflow-hidden border border-gray-200">
  <div class="overflow-x-auto">
    <table class="w-full">
      <thead class="bg-indigo-600 text-white">
        <tr>
          <th class="px-6 py-4 text-sm text-left">ID</th>
          <th class="px-6 py-4 text-sm text-left">Name</th>
          <th class="px-6 py-4 text-sm text-left">Pemilik</th>
          <th class="px-6 py-4 text-sm text-left">Type</th>
          <th class="px-6 py-4 text-sm text-left">Created</th>
          <th class="px-6 py-4 text-sm text-left">Status</th>
          <th class="px-6 py-4 text-sm text-left">Action</th>
        </tr>
      </thead>

      <tbody class="divide-y divide-gray-200">
        @forelse($pengajuans as $p)
          @php
            $t = data_get($p->meta,'tipe','-');
            $actions = $allowedActions($role, $p);
            $isApprovalRole = ($role === 2 || $role === 14);
          @endphp

          <tr class="hover:bg-gray-50">
            <td class="px-6 py-4 text-sm">{{ $p->id }}</td>
            <td class="px-6 py-4 text-sm">{{ $p->judul }}</td>
            <td class="px-6 py-4 text-sm">{{ $p->maker?->name }}</td>
            <td class="px-6 py-4 text-sm">{{ $typeLabel($t) }}</td>
            <td class="px-6 py-4 text-sm">{{ $p->created_at->format('Y-m-d') }}</td>

            <td class="px-6 py-4">
              <span class="inline-flex px-4 py-1.5 rounded-full text-xs border {{ $badgeClass($p) }}">
                {{ $statusText($p) }}
              </span>
            </td>

            <td class="px-6 py-4">
              @if($isApprovalRole)
                <a href="{{ route('pengajuans.show',$p->id) }}"
                   class="px-4 py-1.5 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700">
                  View
                </a>
              @else
                <details class="relative inline-block">
                  <summary class="list-none cursor-pointer inline-flex w-9 h-9 items-center justify-center rounded-lg border hover:bg-gray-50">
                    ⋮
                  </summary>
                  <div class="absolute right-0 mt-2 w-44 bg-white border rounded-lg shadow-lg z-50">
                    @if(in_array('view',$actions))
                      <a href="{{ route('pengajuans.show',$p->id) }}" class="block px-4 py-2 text-sm hover:bg-gray-50">👁 View</a>
                    @endif
                    @if(in_array('edit',$actions))
                      <a href="{{ route('pengajuans.edit',$p->id) }}" class="block px-4 py-2 text-sm hover:bg-gray-50">✏️ Edit</a>
                    @endif
                    @if(in_array('delete',$actions))
                      <form method="POST"
      action="{{ route('pengajuans.destroy', $p->id) }}"
      onsubmit="return confirm('Apakah Anda yakin menghapus form ini?')">
  @csrf
  @method('DELETE')
                        <button class="w-full text-left px-4 py-2 text-sm hover:bg-gray-50 text-red-600">
                          🗑 Delete
                        </button>
                      </form>
                    @endif
                  </div>
                </details>
              @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" class="px-6 py-10 text-center text-slate-500">Tidak ada data.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</section>
@if ($pengajuans instanceof \Illuminate\Pagination\LengthAwarePaginator && $pengajuans->lastPage() > 1)
  <div class="flex items-center justify-center gap-4 py-8">

{{-- Pagination UI (REAL paginate, DESIGN TETAP) --}}
@if ($pengajuans instanceof \Illuminate\Pagination\LengthAwarePaginator && $pengajuans->lastPage() > 1)
  <div class="flex items-center justify-center gap-2 py-8">

    {{-- PREVIOUS --}}
    @if ($pengajuans->onFirstPage())
      <button type="button"
        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-400 bg-white cursor-not-allowed">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"
             stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
          <polyline points="15 18 9 12 15 6"></polyline>
        </svg>
      </button>
    @else
      <a href="{{ $pengajuans->previousPageUrl() }}"
         class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"
             stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
          <polyline points="15 18 9 12 15 6"></polyline>
        </svg>
      </a>
    @endif

    {{-- PAGE INFO --}}
    <span class="px-4 py-2 text-sm text-gray-700">
      {{ $pengajuans->currentPage() }}/{{ $pengajuans->lastPage() }}
    </span>

    {{-- NEXT --}}
    @if ($pengajuans->hasMorePages())
      <a href="{{ $pengajuans->nextPageUrl() }}"
         class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"
             stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
          <polyline points="9 18 15 12 9 6"></polyline>
        </svg>
      </a>
    @else
      <button type="button"
        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-400 bg-white cursor-not-allowed">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"
             stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
          <polyline points="9 18 15 12 9 6"></polyline>
        </svg>
      </button>
    @endif

  </div>
@endif



  </div>
@endif

</div>
@endsection
