@extends('layouts.app')

@section('content')
@php
  // label tipe sesuai request: internal => Pengembangan
  $typeLabel = function($val){
      $v = strtolower((string)$val);
      return match($v){
          'internal' => 'Pengembangan',
          'pengadaan' => 'Pengadaan',
          default => '-',
      };
  };

  // badge warna status (approved hijau, rejected merah)
  $badgeClass = function($code){
      $code = strtolower((string)$code);
      return match($code){
        'approved' => 'border-green-600 text-green-600 bg-green-50',
        'rejected' => 'border-red-600 text-red-600 bg-red-50',
        default    => 'border-slate-300 text-slate-600 bg-slate-50',
      };
  };

  // teks status yang kamu pakai sebelumnya (Approved/Rejected by siapa)
  $statusText = function($p){
      $code = strtolower($p->status?->code ?? '');

      // ambil actor terakhir dari meta decision.steps (kalau ada)
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
        'approved' => $actorN ? "Approved By {$actorN}" : "Approved",
        'rejected' => $actorN ? "Rejected By {$actorN}" : "Rejected",
        default    => $p->status?->label ?? 'Waiting For Approval',
      };
  };
@endphp

<style>
  @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap');
  .qpri-font { font-family: 'Inter', sans-serif; }
  .hover-lift { transition: transform 200ms ease-in, background-color 200ms ease-in; }
  .hover-lift:hover { transform: translateY(-2px); }
</style>

<div class="qpri-font">

  {{-- TOP BAR (SEARCH + FILTER) --}}
  <form method="GET" action="{{ url()->current() }}">
    <div class="bg-white border border-gray-200 rounded-lg p-6 mb-6">
      <div class="flex items-center justify-between gap-6">

        <div class="flex-1 max-w-md">
          <div class="relative">
            <input
              type="search"
              name="q"
              value="{{ $q ?? '' }}"
              placeholder="Search"
              class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
            >
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <circle cx="11" cy="11" r="8"></circle>
              <path d="m21 21-4.35-4.35"></path>
            </svg>
          </div>
        </div>

        <div class="relative w-52">
          <select
            name="type"
            class="w-full pl-4 pr-10 py-2 border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent appearance-none cursor-pointer"
            onchange="this.form.submit()"
          >
            <option value="all" {{ ($type ?? 'all') === 'all' ? 'selected' : '' }}>Type : All</option>
            <option value="internal" {{ ($type ?? '') === 'internal' ? 'selected' : '' }}>Type : Pengembangan</option>
            <option value="pengadaan" {{ ($type ?? '') === 'pengadaan' ? 'selected' : '' }}>Type : Pengadaan</option>
          </select>
          <svg class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <polyline points="6 9 12 15 18 9"></polyline>
          </svg>
        </div>

        <button type="submit" class="hidden">Submit</button>
      </div>
    </div>
  </form>

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

  {{-- APPROVED TABLE --}}
  <section class="bg-white rounded-lg overflow-hidden border border-gray-200 mb-8">
    <div class="overflow-x-auto">
      <table class="w-full">
        <thead class="bg-indigo-600">
          <tr>
            <th class="px-6 py-4 text-left text-sm font-medium text-white">ID</th>
            <th class="px-6 py-4 text-left text-sm font-medium text-white">Name</th>
            <th class="px-6 py-4 text-left text-sm font-medium text-white">Pemilik</th>
            <th class="px-6 py-4 text-left text-sm font-medium text-white">Type</th>
            <th class="px-6 py-4 text-left text-sm font-medium text-white">Created</th>
            <th class="px-6 py-4 text-left text-sm font-medium text-white">Status</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          @forelse(($approvedList ?? []) as $p)
            @php $t = data_get($p->meta,'tipe','-'); @endphp
            <tr class="hover:bg-gray-50">
              <td class="px-6 py-4 text-sm text-gray-900">{{ $p->id }}</td>
              <td class="px-6 py-4 text-sm text-gray-900">{{ $p->judul ?? '-' }}</td>
              <td class="px-6 py-4 text-sm text-gray-900">{{ $p->maker?->name ?? '-' }}</td>
              <td class="px-6 py-4 text-sm text-gray-900">{{ $typeLabel($t) }}</td>
              <td class="px-6 py-4 text-sm text-gray-900">{{ optional($p->created_at)->format('Y-m-d') }}</td>
              <td class="px-6 py-4">
                <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-medium border {{ $badgeClass($p->status?->code) }}">
                  {{ $statusText($p) }}
                </span>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="px-6 py-10 text-center text-slate-500">Belum ada data approved.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </section>

  {{-- REJECTED TABLE --}}
  <section class="bg-white rounded-lg overflow-hidden border border-gray-200 mb-8">
    <div class="overflow-x-auto">
      <table class="w-full">
        <thead class="bg-indigo-600">
          <tr>
            <th class="px-6 py-4 text-left text-sm font-medium text-white">ID</th>
            <th class="px-6 py-4 text-left text-sm font-medium text-white">Name</th>
            <th class="px-6 py-4 text-left text-sm font-medium text-white">Pemilik</th>
            <th class="px-6 py-4 text-left text-sm font-medium text-white">Type</th>
            <th class="px-6 py-4 text-left text-sm font-medium text-white">Created</th>
            <th class="px-6 py-4 text-left text-sm font-medium text-white">Status</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          @forelse(($rejectedList ?? []) as $p)
            @php $t = data_get($p->meta,'tipe','-'); @endphp
            <tr class="hover:bg-gray-50">
              <td class="px-6 py-4 text-sm text-gray-900">{{ $p->id }}</td>
              <td class="px-6 py-4 text-sm text-gray-900">{{ $p->judul ?? '-' }}</td>
              <td class="px-6 py-4 text-sm text-gray-900">{{ $p->maker?->name ?? '-' }}</td>
              <td class="px-6 py-4 text-sm text-gray-900">{{ $typeLabel($t) }}</td>
              <td class="px-6 py-4 text-sm text-gray-900">{{ optional($p->created_at)->format('Y-m-d') }}</td>
              <td class="px-6 py-4">
                <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-medium border {{ $badgeClass($p->status?->code) }}">
                  {{ $statusText($p) }}
                </span>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="px-6 py-10 text-center text-slate-500">Belum ada data rejected.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </section>

  {{-- Pagination UI (tetap tampil sesuai desain kamu; kalau mau real paginate nanti kita rapihin) --}}
  <div class="flex items-center justify-center gap-2 py-8">
    <button type="button" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition">
      <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
        <polyline points="15 18 9 12 15 6"></polyline>
      </svg>
    </button>
    <span class="px-4 py-2 text-sm text-gray-700">1/10</span>
    <button type="button" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition">
      <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
        <polyline points="9 18 15 12 9 6"></polyline>
      </svg>
    </button>
  </div>

</div>
@endsection