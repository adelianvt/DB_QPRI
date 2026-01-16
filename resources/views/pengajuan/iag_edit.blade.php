<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Formulir Registrasi Project</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap');
    body { font-family: 'Inter', sans-serif; }
    .checkbox-item { transition: background-color 0.2s ease; }
    .checkbox-item:hover { background-color: #f9fafb; }
    .modal-overlay { animation: fadeIn 0.3s ease-out; }
    .modal-content { animation: slideUp 0.3s ease-out; }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes slideUp { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
  </style>
</head>

@php
  $meta = (array) ($pengajuan->meta ?? []);
  $iag  = (array) data_get($meta, 'iag', []);

  $contactName  = data_get($meta, 'contact.nama', '-');
  $contactPhone = data_get($meta, 'contact.hp', '-');
  $contactEmail = data_get($meta, 'contact.email', '-');

  $groups1 = (array) data_get($meta, 'group_utama', []);
  $groups2 = (array) data_get($meta, 'group_utama_2', []);
  $compiler = (array) data_get($meta, 'compiler_names', []);

  $rbbUsers = (array) data_get($meta,'rbb_users', []);
  $rbbIt    = (array) data_get($meta,'rbb_it', []);

$itagSaved = old('iag.itag_list', $iag['itag_list'] ?? []);
$itwSaved  = old('iag.itw_list', $iag['itw_list'] ?? []);
$karSaved  = old('iag.karakter', $iag['karakter'] ?? []);


  $itagOptions = [
    'Ipmawan Sukarpiana',
    'Roni Welem Akyuwen',
    'Rachmat Rasidi',
    'Ananias Ardiles Sembirin',
    'Marina Wanda Putri',
    'Mellisa Lasilkvie Sennivena',
    'Poppy Kinantya Pratiwi',
    'Alivia Talitha',
    'Ira Febriyanti',
  ];

  $itwOptions = [
    'Alivia Talitha',
    'Ira Febriyanti',
    'Poppy Kinantya Pratiwi',
    'Elvetta Hayatunnisa Barnitan',
  ];

  $karOptions = [
    'E Banking',
    'Jasa TI',
    'Laporan',
    'Infrastruktur TI',
    'Pemeliharaan Infrastruktur TI',
    'Pemeliharaan Aplikasi',
    'Transaksional Bisnis',
    'Supporting Bisnis',
    'Sistem Pendukung IT',
  ];
@endphp

<body class="bg-gray-50">

  <!-- Header -->
  <header class="bg-indigo-600 text-white py-4 px-4 flex items-center gap-4 sticky top-0 z-50 shadow-md">
    <button class="hover:bg-indigo-700 p-2 rounded-lg transition"
            onclick="window.location.href='{{ route('pengajuans.index') }}'"
            aria-label="Back">
      <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
        <polyline points="15 18 9 12 15 6"></polyline>
      </svg>
    </button>
    <h1 class="text-lg font-semibold text-center">FORMULIR REGISTRASI PROJECT</h1>
  </header>

  <main class="max-w-2xl mx-auto px-4 py-6 pb-24 space-y-8">

    <!-- ===== BAGIAN CRV (READONLY) ===== -->
    <section class="bg-white border border-gray-200 rounded-xl p-5 space-y-4">
      <div class="flex items-start justify-between gap-3">
        <div>
          <div class="text-sm font-semibold text-gray-900">Pengajuan #{{ $pengajuan->id }}</div>
          <div class="text-xs text-gray-500">{{ $pengajuan->judul }}</div>
        </div>
        <div class="text-xs text-gray-500">Status: <b>{{ $pengajuan->status?->label ?? '-' }}</b></div>
      </div>

      <div>
        <div class="text-xs font-semibold text-gray-700 mb-1">Pengajuan Nama Project</div>
        <div class="border rounded-lg px-4 py-3 bg-gray-50 text-sm">{{ $pengajuan->judul ?? '-' }}</div>
      </div>

      <div>
        <div class="text-xs font-semibold text-gray-700 mb-1">Deskripsi</div>
        <div class="border rounded-lg px-4 py-3 bg-gray-50 text-sm whitespace-pre-wrap">{{ $pengajuan->deskripsi ?? '-' }}</div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <div>
          <div class="text-xs font-semibold text-gray-700 mb-1">Divisi</div>
          <div class="border rounded-lg px-4 py-3 bg-gray-50 text-sm">{{ data_get($meta,'divisi','-') }}</div>
        </div>
        <div>
          <div class="text-xs font-semibold text-gray-700 mb-1">Tipe</div>
          <div class="border rounded-lg px-4 py-3 bg-gray-50 text-sm">{{ data_get($meta,'tipe','-') }}</div>
        </div>
      </div>

      <div>
        <div class="text-xs font-semibold text-gray-700 mb-1">Group Utama (1)</div>
        <div class="border rounded-lg px-4 py-3 bg-gray-50 text-sm">
          @if(count($groups1))
            <ul class="list-disc pl-5 space-y-1">
              @foreach($groups1 as $x)<li>{{ $x }}</li>@endforeach
            </ul>
          @else - @endif
        </div>
      </div>

      <div>
        <div class="text-xs font-semibold text-gray-700 mb-2">Contact Person</div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
          <div class="border rounded-lg px-4 py-3 bg-gray-50">
            <div class="text-[11px] text-gray-500 mb-1">Nama</div>
            <div class="text-sm font-medium">{{ $contactName }}</div>
          </div>
          <div class="border rounded-lg px-4 py-3 bg-gray-50">
            <div class="text-[11px] text-gray-500 mb-1">No HP</div>
            <div class="text-sm font-medium">{{ $contactPhone }}</div>
          </div>
          <div class="border rounded-lg px-4 py-3 bg-gray-50">
            <div class="text-[11px] text-gray-500 mb-1">Email</div>
            <div class="text-sm font-medium">{{ $contactEmail }}</div>
          </div>
        </div>
      </div>

      <div>
        <div class="text-xs font-semibold text-gray-700 mb-1">Group Utama (2)</div>
        <div class="border rounded-lg px-4 py-3 bg-gray-50 text-sm">
          @if(count($groups2))
            <ul class="list-disc pl-5 space-y-1">
              @foreach($groups2 as $x)<li>{{ $x }}</li>@endforeach
            </ul>
          @else - @endif
        </div>
      </div>

      <div>
        <div class="text-xs font-semibold text-gray-700 mb-1">Penyusun</div>
        <div class="border rounded-lg px-4 py-3 bg-gray-50 text-sm">
          @if(count($compiler))
            <ul class="list-disc pl-5 space-y-1">
              @foreach($compiler as $x)<li>{{ $x }}</li>@endforeach
            </ul>
          @else - @endif
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <div class="border rounded-lg overflow-hidden">
          <div class="bg-indigo-600 text-white px-4 py-2 text-xs font-semibold">RBB Divisi Users</div>
          <div class="p-4 space-y-2 bg-gray-50 text-sm">
            <div><b>Kode:</b> {{ data_get($rbbUsers,'kode','-') }}</div>
            <div><b>Nama:</b> {{ data_get($rbbUsers,'nama','-') }}</div>
            <div><b>Anggaran:</b> {{ data_get($rbbUsers,'anggaran','-') }}</div>
          </div>
        </div>

        <div class="border rounded-lg overflow-hidden">
          <div class="bg-indigo-600 text-white px-4 py-2 text-xs font-semibold">RBB Divisi IT</div>
          <div class="p-4 space-y-2 bg-gray-50 text-sm">
            <div><b>Kode:</b> {{ data_get($rbbIt,'kode','-') }}</div>
            <div><b>Nama:</b> {{ data_get($rbbIt,'nama','-') }}</div>
            <div><b>Bundling:</b> {{ data_get($rbbIt,'bundling','-') }}</div>
            <div><b>Anggaran:</b> {{ data_get($rbbIt,'anggaran','-') }}</div>
          </div>
        </div>
      </div>
    </section>

    <!-- ===== FORM TAMBAHAN IAG ===== -->
    <form id="projectForm"
          class="space-y-6"
          method="POST"
          action="{{ route('pengajuans.iag.update', $pengajuan->id) }}">
      @csrf
      @method('PUT')

      <!-- Kode Project -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Kode Project <span class="text-red-500">*</span></label>
        <input type="text" name="iag[kode_project]" value="{{ old('iag.kode_project', $iag['kode_project'] ?? '') }}"
          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
        @error('iag.kode_project') <p class="text-xs text-red-600 mt-2">{{ $message }}</p> @enderror
      </div>

      <!-- Nama Project -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Project <span class="text-red-500">*</span></label>
        <input type="text" name="iag[nama_project]" value="{{ old('iag.nama_project', $iag['nama_project'] ?? '') }}"
          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
        @error('iag.nama_project') <p class="text-xs text-red-600 mt-2">{{ $message }}</p> @enderror
      </div>

      <!-- IT Architecture & Governance -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-3">IT Architectur & Governance <span class="text-red-500">*</span></label>
        <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 space-y-2 bg-white">
          @foreach($itagOptions as $nm)
            <label class="flex items-start gap-3 cursor-pointer checkbox-item p-2 rounded">
              <input type="checkbox" name="iag[itag_list][]" value="{{ $nm }}"
                     class="w-4 h-4 text-indigo-600 focus:ring-indigo-500 mt-0.5"
                     {{ in_array($nm, $itagSaved) ? 'checked' : '' }}>
              <span class="text-sm text-gray-700">{{ $nm }}</span>
            </label>
          @endforeach
        </div>
        @error('iag.itag_list') <p class="text-xs text-red-600 mt-2">{{ $message }}</p> @enderror
      </div>

      <!-- IT Technical Writer -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-3">IT Technical Writer <span class="text-red-500">*</span></label>
        <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 space-y-2 bg-white">
          @foreach($itwOptions as $nm)
            <label class="flex items-start gap-3 cursor-pointer checkbox-item p-2 rounded">
              <input type="checkbox" name="iag[itw_list][]" value="{{ $nm }}"
                     class="w-4 h-4 text-indigo-600 focus:ring-indigo-500 mt-0.5"
                     {{ in_array($nm, $itwSaved) ? 'checked' : '' }}>
              <span class="text-sm text-gray-700">{{ $nm }}</span>
            </label>
          @endforeach
        </div>
        @error('iag.itw_list') <p class="text-xs text-red-600 mt-2">{{ $message }}</p> @enderror
      </div>

      <!-- Karakteristik Proyek -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-3">Karakteristik Proyek <span class="text-red-500">*</span></label>
        <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 space-y-2 bg-white">
          @foreach($karOptions as $nm)
            <label class="flex items-start gap-3 cursor-pointer checkbox-item p-2 rounded">
              <input type="checkbox" name="iag[karakter][]" value="{{ $nm }}"
                     class="w-4 h-4 text-indigo-600 focus:ring-indigo-500 mt-0.5"
                     {{ in_array($nm, $karSaved) ? 'checked' : '' }}>
              <span class="text-sm text-gray-700">{{ $nm }}</span>
            </label>
          @endforeach
        </div>
        @error('iag.karakter') <p class="text-xs text-red-600 mt-2">{{ $message }}</p> @enderror
      </div>

      <!-- Footer Info -->
      <div class="text-center py-8 space-y-2">
        <p class="text-sm text-gray-600">BMDM - Juni 2025</p>
        <p class="text-sm font-medium text-gray-700">Group Head IT Architecture & Governance</p>
      </div>

      <section>
        <label class="block text-sm font-medium text-gray-700 mb-3 text-center">Approval</label>
        <div class="w-48 h-48 bg-indigo-100 border-2 border-dashed border-indigo-300 rounded-lg mx-auto"></div>
      </section>

      <button id="realSubmit" type="submit" class="hidden">Submit</button>
    </form>
  </main>

  <!-- Fixed Submit Button -->
  <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 p-4">
    <div class="max-w-2xl mx-auto flex justify-end">
      <button type="button" id="submitBtn"
        class="px-8 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-medium">
        Submit
      </button>
    </div>
  </div>

  <!-- Submit Confirmation Modal -->
  <div id="submitConfirmModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4 modal-overlay">
    <div class="bg-white rounded-lg max-w-md w-full p-6 modal-content">
      <div class="text-center mb-6">
        <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
          <svg class="text-indigo-600" width="32" height="32" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
            <polyline points="22 4 12 14.01 9 11.01"></polyline>
          </svg>
        </div>
        <h2 class="text-xl font-semibold text-gray-900 mb-2">Submit Project</h2>
        <p class="text-sm text-gray-600">Are you sure want to submit this project ?</p>
      </div>

      <div class="flex gap-3 justify-center">
        <button id="submitNoBtn"
          class="px-8 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium">
          Cancel
        </button>
        <button id="submitYesBtn"
          class="px-8 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-medium">
          Yes, Submit
        </button>
      </div>
    </div>
  </div>

  <script>
    const submitBtn = document.getElementById('submitBtn');
    const submitConfirmModal = document.getElementById('submitConfirmModal');
    const submitYesBtn = document.getElementById('submitYesBtn');
    const submitNoBtn = document.getElementById('submitNoBtn');
    const realSubmit = document.getElementById('realSubmit');

    submitBtn.addEventListener('click', () => submitConfirmModal.classList.remove('hidden'));
    submitNoBtn.addEventListener('click', () => submitConfirmModal.classList.add('hidden'));
    submitYesBtn.addEventListener('click', () => {
      submitConfirmModal.classList.add('hidden');
      realSubmit.click();
    });

    submitConfirmModal.addEventListener('click', (e) => {
      if (e.target === submitConfirmModal) submitConfirmModal.classList.add('hidden');
    });

    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') submitConfirmModal.classList.add('hidden');
    });
  </script>

</body>
</html>