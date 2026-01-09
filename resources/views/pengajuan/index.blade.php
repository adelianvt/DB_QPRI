@extends('layouts.app')

@section('content')

@php
  $user = auth()->user();

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
        default             => $p->status?->label ?? 'Waiting For Approval',
      };
  };

  $badgeClass = function($p){
      $code = strtolower($p->status?->code ?? '');
      return match($code){
          'approved' => 'border-green-600 text-green-600 bg-green-50',
          'rejected' => 'border-red-600 text-red-600 bg-red-50',
          'pending_iag' => 'border-blue-600 text-blue-600 bg-blue-50',
          default    => 'border-yellow-600 text-yellow-600 bg-yellow-50',
      };
  };

  $typeLabel = function($val){
      $v = strtolower((string)$val);
      return match($v){
          'internal' => 'Pengembangan',
          'pengadaan' => 'Pengadaan',
          default => '-',
      };
  };
@endphp


{{-- FILTER --}}
<form method="GET" action="{{ url()->current() }}">
  <div class="bg-white border border-gray-200 rounded-lg p-6 mb-6">
    <div class="flex items-center justify-between gap-6">

      <div class="flex-1 max-w-md">
        <div class="relative">
          <input type="search" name="q" value="{{ $q ?? '' }}" placeholder="Search"
            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <circle cx="11" cy="11" r="8"></circle>
            <path d="m21 21-4.35-4.35"></path>
          </svg>
        </div>
      </div>

      <div class="relative w-52">
        <select name="type"
          class="w-full pl-4 pr-10 py-2 border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent appearance-none cursor-pointer"
          onchange="this.form.submit()">
          <option value="all" {{ ($type ?? 'all') === 'all' ? 'selected' : '' }}>Type : All</option>
          <option value="internal" {{ ($type ?? '') === 'internal' ? 'selected' : '' }}>Pengembangan</option>
          <option value="pengadaan" {{ ($type ?? '') === 'pengadaan' ? 'selected' : '' }}>Pengadaan</option>
        </select>
      </div>
    </div>
  </div>
</form>


{{-- CARD --}}
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

{{-- ✅ FORM DOWNLOAD SELECTED --}}
<form method="POST" action="{{ route('pengajuans.downloadSelected') }}" id="formDownloadSelected">
  @csrf

  <section class="bg-white rounded-lg overflow-hidden border border-gray-200">
    <div class="overflow-x-auto">
      <table class="w-full">
        <thead class="bg-indigo-600">
          <tr>
            <th class="px-6 py-4 text-left text-sm font-medium text-white hidden" id="colCheck">
              <input type="checkbox" id="checkAll" class="w-4 h-4">
            </th>

            <th class="px-6 py-4 text-left text-sm font-medium text-white">ID</th>
            <th class="px-6 py-4 text-left text-sm font-medium text-white">Name</th>
            <th class="px-6 py-4 text-left text-sm font-medium text-white">Pemilik</th>
            <th class="px-6 py-4 text-left text-sm font-medium text-white">Type</th>
            <th class="px-6 py-4 text-left text-sm font-medium text-white">Created</th>
            <th class="px-6 py-4 text-left text-sm font-medium text-white">Status</th>
            <th class="px-6 py-4 text-left text-sm font-medium text-white">Action</th>
          </tr>
        </thead>

        <tbody class="divide-y divide-gray-200">
          @forelse($pengajuans as $p)
            @php $t = data_get($p->meta,'tipe','-'); @endphp
            <tr class="hover:bg-gray-50">
              <td class="px-6 py-4 hidden chk">
                <input type="checkbox" class="rowCheck w-4 h-4" name="ids[]" value="{{ $p->id }}">
              </td>

              <td class="px-6 py-4 text-sm text-gray-900">{{ $p->id }}</td>
              <td class="px-6 py-4 text-sm text-gray-900">{{ $p->judul ?? '-' }}</td>
              <td class="px-6 py-4 text-sm text-gray-900">{{ $p->maker?->name ?? '-' }}</td>
              <td class="px-6 py-4 text-sm text-gray-900">{{ $typeLabel($t) }}</td>
              <td class="px-6 py-4 text-sm text-gray-900">{{ optional($p->created_at)->format('Y-m-d') }}</td>

              <td class="px-6 py-4">
                <span class="inline-flex items-center justify-center w-56 px-4 py-1.5 rounded-full text-xs font-medium border {{ $badgeClass($p) }}">
                  {{ $statusText($p) }}
                </span>
              </td>

              <td class="px-6 py-4">
                <a href="{{ route('pengajuans.show', $p->id) }}"
                   class="inline-flex items-center gap-2 px-4 py-1.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition text-sm">
                  View
                </a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" class="px-6 py-10 text-center text-slate-500">Tidak ada data.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </section>
</form>

@endsection


{{-- ✅ FIXED BUTTON PASTI MUNCUL SEMUA ROLE --}}
@push('fixed')
<div class="fixed bottom-6 right-6 z-[999999] flex items-center gap-2 pointer-events-auto">

  <button type="button" id="btnDownload"
    class="bg-indigo-600 text-white px-5 py-2 rounded-lg shadow-lg hover:bg-indigo-700 transition">
    Download
  </button>

  <button type="button" id="btnSelected"
    class="hidden bg-indigo-600 text-white px-5 py-2 rounded-lg shadow-lg hover:bg-indigo-700 transition">
    Selected Download
  </button>

  <button type="button" id="btnCancel"
    class="hidden border bg-white px-5 py-2 rounded-lg shadow-lg hover:bg-gray-100 transition">
    Cancel
  </button>

</div>
@endpush


@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function(){

  const form = document.getElementById("formDownloadSelected");

  const btnD = document.getElementById("btnDownload");
  const btnS = document.getElementById("btnSelected");
  const btnC = document.getElementById("btnCancel");
  const col  = document.getElementById("colCheck");
  const checkAll = document.getElementById("checkAll");

  function setDownloadMode(on){
    if(on){
      col?.classList.remove("hidden");
      document.querySelectorAll(".chk").forEach(c=>c.classList.remove("hidden"));

      btnD?.classList.add("hidden");
      btnS?.classList.remove("hidden");
      btnC?.classList.remove("hidden");
    } else {
      col?.classList.add("hidden");
      document.querySelectorAll(".chk").forEach(c=>c.classList.add("hidden"));

      if(checkAll) checkAll.checked = false;
      document.querySelectorAll(".rowCheck").forEach(cb=>cb.checked = false);

      btnD?.classList.remove("hidden");
      btnS?.classList.add("hidden");
      btnC?.classList.add("hidden");
    }
  }

  btnD?.addEventListener("click", ()=>setDownloadMode(true));
  btnC?.addEventListener("click", ()=>setDownloadMode(false));

  btnS?.addEventListener("click", ()=>{
    form.submit();
  });

  checkAll?.addEventListener("change", (e)=>{
    const on = !!e.target.checked;
    document.querySelectorAll(".rowCheck").forEach(cb=>cb.checked = on);
  });

});
</script>
@endpush