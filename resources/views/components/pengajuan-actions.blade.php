@props([
  'pengajuan' => null,
  'p' => null,
  'role' => 'MAKER',  // MAKER | APPROVER | ADMIN
])

@php
  $pengajuan = $pengajuan ?? $p;

  // safety biar ga 500 walau kepanggil tanpa data
  if (!$pengajuan) {
    echo '<span class="text-sm text-gray-400">-</span>';
    return;
  }

  $status = strtolower($pengajuan->status?->code ?? '');

  // waiting: sesuaikan kalau status code kamu beda
  $isWaiting  = in_array($status, ['draft','submitted','waiting','under_review','checking','checking_by_iag']);
  $isApproved = in_array($status, ['approved','approved_by_gh_crv','approved_by_gh_iag']);
  $isRejected = in_array($status, ['rejected','rejected_by_gh_crv','rejected_by_gh_iag']);

  if (!$isWaiting && !$isApproved && !$isRejected) $isWaiting = true;

  $role = strtoupper($role);

  // RULES tombol
  $canView   = true;
  $canEdit   = false;
  $canDelete = false;

  if ($role === 'MAKER') {
    if ($isRejected) { $canEdit = true; $canDelete = true; }
  } elseif ($role === 'APPROVER') {
    // approver hanya view
  } elseif ($role === 'ADMIN') {
    if ($isWaiting) { $canEdit = true; $canDelete = true; }
    if ($isApproved || $isRejected) { $canDelete = true; }
  }

  $dropdownId = 'dropdown-' . $role . '-' . $pengajuan->id;

  // =========================
  // ROUTE PREFIX BERDASARKAN ROLE
  // =========================
  $isAdmin = $role === 'ADMIN';
  $showRoute    = $isAdmin ? 'admin.pengajuans.show'    : 'pengajuans.show';
  $editRoute    = $isAdmin ? 'admin.pengajuans.edit'    : 'pengajuans.edit';
  $destroyRoute = $isAdmin ? 'admin.pengajuans.destroy' : 'pengajuans.destroy';

  // safe route: kalau route belum ada, jangan 500
  $viewUrl   = \Illuminate\Support\Facades\Route::has($showRoute)    ? route($showRoute, $pengajuan->id)    : '#';
  $editUrl   = \Illuminate\Support\Facades\Route::has($editRoute)    ? route($editRoute, $pengajuan->id)    : '#';
  $deleteUrl = \Illuminate\Support\Facades\Route::has($destroyRoute) ? route($destroyRoute, $pengajuan->id) : '#';
@endphp

<div class="relative inline-block text-left">
  <button type="button"
          class="inline-flex items-center justify-center w-9 h-9 rounded-full hover:bg-gray-100 transition"
          onclick="toggleDropdown(event, '{{ $dropdownId }}')"
          aria-haspopup="true" aria-expanded="false">
    <svg class="w-5 h-5 text-gray-600" viewBox="0 0 20 20" fill="currentColor">
      <path d="M10 3a1.5 1.5 0 110 3 1.5 1.5 0 010-3zm0 5.5a1.5 1.5 0 110 3 1.5 1.5 0 010-3zM10 14a1.5 1.5 0 110 3 1.5 1.5 0 010-3z"/>
    </svg>
  </button>

  <div id="{{ $dropdownId }}"
       class="hidden absolute right-0 mt-2 w-40 origin-top-right bg-white border border-gray-200 rounded-lg shadow-lg z-50">
    <div class="py-1">

      @if($canView)
        <a href="{{ $viewUrl }}"
           class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
          👁️ <span>View</span>
        </a>
      @endif

      @if($canEdit)
        <a href="{{ $editUrl }}"
           class="flex items-center gap-2 px-4 py-2 text-sm text-indigo-600 hover:bg-gray-50">
          ✏️ <span>Edit</span>
        </a>
      @endif

      @if($canDelete)
        <form action="{{ $deleteUrl }}" method="POST"
              onsubmit="return confirm('Yakin mau hapus pengajuan ini?');">
          @csrf
          @method('DELETE')
          <button type="submit"
                  class="w-full text-left flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-gray-50">
            🗑️ <span>Delete</span>
          </button>
        </form>
      @endif

    </div>
  </div>
</div>
