@extends('layouts.app')

@section('content')
<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <title>Formulir Registrasi Project</title>
    <script src="https://cdn.tailwindcss.com"></script>
  </head>

  <body class="bg-[#407BFF] min-h-screen">

    <!-- Header -->
    <header class="bg-blue-600 text-white px-6 py-4 shadow-lg">
      <div class="flex items-center justify-center relative">
        <button type="button" onclick="window.location.href='{{ route('pengajuans.index') }}'"
          class="absolute left-6 p-2 hover:bg-blue-700 rounded-lg transition-colors">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
          </svg>
        </button>
        <h1 class="text-xl font-semibold">FORMULIR REGISTRASI PROJECT</h1>
      </div>
    </header>

    <!-- PAGE CONTENT -->
    <main class="bg-[#F5F7FB] min-h-[calc(100vh-56px)]">
      <div class="max-w-4xl mx-auto py-10 px-4">

        <!-- FORM START -->
        <form id="projectForm" class="space-y-6" method="POST"
      action="{{ route('pengajuans.store') }}" novalidate>
          @csrf

          <!-- Nama Project -->
          <div class="bg-white rounded-lg shadow-sm p-6">
            <label class="block text-xs font-semibold">
              Pengajuan Nama Project <span class="text-red-500">*</span>
            </label>
            <input
              id="projectName"
              name="judul"
              type="text"
              required
              value="{{ old('judul') }}"
              class="w-full border px-4 py-2.5 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-[#407BFF]"
              placeholder="Nama Project"
            >
            @error('judul')
              <p class="text-xs text-red-600 mt-2">{{ $message }}</p>
            @enderror

          </div>

          <!-- Deskripsi (WAJIB supaya tidak null di DB) -->
          <div class="bg-white rounded-lg shadow-sm p-6">
            <label class="block text-xs font-semibold">
              Deskripsi <span class="text-red-500">*</span>
            </label>
            <textarea
              name="deskripsi"
              required
              rows="4"
              class="w-full border px-4 py-2.5 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-[#407BFF]"
              placeholder="Tulis deskripsi project..."
            >{{ old('deskripsi') }}</textarea>
            @error('deskripsi')
              <p class="text-xs text-red-600 mt-2">{{ $message }}</p>
            @enderror
          </div>

          <!-- Divisi -->
          <div class="bg-white rounded-lg shadow-sm p-6">
            <label class="block text-xs font-semibold">
              Divisi Yang Menginisiasikan <span class="text-red-500">*</span>
            </label>
            <select name="divisi" id="divisionSelect" required
              class="w-full border border-[#407BFF] px-4 py-2.5 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-[#407BFF]">
              <option value="">Pilih Divisi</option>
              <option value="Divisi Human Capital" {{ old('divisi')=='Divisi Human Capital'?'selected':'' }}>Divisi Human Capital</option>
              <option value="Divisi 1" {{ old('divisi')=='Divisi 1'?'selected':'' }}>Divisi 1</option>
              <option value="Divisi 2" {{ old('divisi')=='Divisi 2'?'selected':'' }}>Divisi 2</option>
            </select>
            @error('divisi')
              <p class="text-xs text-red-600 mt-2">{{ $message }}</p>
            @enderror
          </div>

          <!-- Tipe Project -->
          <div class="bg-white rounded-lg shadow-sm p-6">
            <p class="text-xs font-semibold">
              Tipe Project <span class="text-red-500">*</span>
            </p>
            <div class="mt-1 space-y-1 text-sm">
              <label class="flex gap-2 items-center">
                <input type="radio" name="tipe" value="internal" class="w-4 h-4 text-[#407BFF]" required
                  {{ old('tipe')=='internal'?'checked':'' }}>
                <span>Pengembangan Internal</span>
              </label>
              <label class="flex gap-2 items-center">
                <input type="radio" name="tipe" value="pengadaan" class="w-4 h-4 text-[#407BFF]" required
                  {{ old('tipe')=='pengadaan'?'checked':'' }}>
                <span>Pengadaan</span>
              </label>
            </div>
            @error('tipe')
              <p class="text-xs text-red-600 mt-2">{{ $message }}</p>
            @enderror
          </div>

 <!-- GROUP UTAMA 1 -->
          <div class="bg-white rounded-lg shadow-sm p-6">
            <p class="text-xs font-semibold">Group Utama <span class="text-red-500">*</span></p>
            <div class="border-2 border-dashed px-4 py-3 rounded-lg text-sm">
              @php $oldG1 = old('group_utama', []); @endphp
              @foreach(['IT Architecture & Governance','Corporate Service','Application Management Management','Digital Business','Business Intelligence, Analytics & Regulatory','Backend Management','Helpdesk & Support','Operation Management DC','IT Security'] as $opt)
                <label class="flex gap-2">
                  <input type="checkbox" name="group_utama[]" value="{{ $opt }}" {{ in_array($opt,$oldG1)?'checked':'' }}>
                  {{ $opt }}
                </label>
              @endforeach
            </div>

            {{-- ✅ FIX VALIDASI --}}
            @error('group_utama')
<p class="text-xs text-red-600 mt-2">{{ $message }}</p>
@enderror
@error('group_utama.0')
<p class="text-xs text-red-600 mt-2">{{ $message }}</p>
@enderror

          </div>


          <!-- ✅ Contact Person (WATERMARK TETAP - SESUAI GAMBAR) -->
          @php
            $wmName  = 'Sani Hardiansa';
            $wmPhone = '+62 812-2005-343';
            $wmEmail = 'shardiansa@BANKBJB.CO.ID';
          @endphp

          <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-sm font-medium text-gray-700 mb-4">
              Contact Person (Grup User/Nama Group Penanggungjawab Pekerjaan) <span class="text-red-500">*</span>
            </h3>

            <div class="space-y-4">
              <div>
                <label for="contactName" class="block text-xs font-medium text-gray-600 mb-2">Nama</label>
                <input
                  type="text"
                  id="contactName"
                  readonly
                  value="{{ $wmName }}"
                  class="w-full px-3 py-2.5 border border-gray-300 rounded-md text-sm text-gray-900 bg-gray-50">
                <input type="hidden" name="contact[nama]" value="{{ $wmName }}">
              </div>

              <div>
                <label for="contactPhone" class="block text-xs font-medium text-gray-600 mb-2">No Handphone</label>
                <input
                  type="tel"
                  id="contactPhone"
                  readonly
                  value="{{ $wmPhone }}"
                  class="w-full px-3 py-2.5 border border-gray-300 rounded-md text-sm text-gray-900 bg-gray-50">
                <input type="hidden" name="contact[hp]" value="{{ $wmPhone }}">
              </div>

              <div>
                <label for="contactEmail" class="block text-xs font-medium text-gray-600 mb-2">Email</label>
                <input
                  type="email"
                  id="contactEmail"
                  readonly
                  value="{{ $wmEmail }}"
                  class="w-full px-3 py-2.5 border border-gray-300 rounded-md text-sm text-gray-900 bg-gray-50">
                <input type="hidden" name="contact[email]" value="{{ $wmEmail }}">
              </div>
            </div>
          </div>

          <!-- GROUP UTAMA 2 -->
          <div class="bg-white rounded-lg shadow-sm p-6">
            <p class="text-xs font-semibold">Group Utama 2 <span class="text-red-500">*</span></p>
            <div class="border-2 border-dashed px-4 py-3 rounded-lg text-sm">
              @php $oldG2 = old('group_utama_2', []); @endphp
              @foreach(['IT Architecture & Governance','Corporate Service','Application Management Management','Digital Business','Business Intelligence, Analytics & Regulatory','Backend Management','Helpdesk & Support','Operation Management DC','IT Security'] as $opt)
                <label class="flex gap-2">
                  <input type="checkbox" name="group_utama_2[]" value="{{ $opt }}" {{ in_array($opt,$oldG2)?'checked':'' }}>
                  {{ $opt }}
                </label>
              @endforeach
            </div>

            {{-- ✅ FIX VALIDASI --}}
            @error('group_utama_2')
<p class="text-xs text-red-600 mt-2">{{ $message }}</p>
@enderror
@error('group_utama_2.0')
<p class="text-xs text-red-600 mt-2">{{ $message }}</p>
@enderror
          </div>

          <!-- Penyusun -->
          <!-- Penyusun -->
<div class="bg-white rounded-lg shadow-sm p-6">
  <p class="text-xs font-semibold">
    Nama Lengkap Penyusun Dokumen Pengembangan/Pengadaan
    <span class="text-red-500">*</span>
  </p>

  <div id="compilerWrapper" class="space-y-2">
    <div id="compilerForm" class="space-y-2"></div>

    @php $oldComp = old('compiler_names', []); @endphp
    @foreach($oldComp as $nm)
      <input type="hidden" name="compiler_names[]" value="{{ $nm }}">
      <div class="rounded-md border bg-white px-4 py-2 text-gray-800">
        {{ $nm }}
      </div>
    @endforeach

    <button id="addRow" type="button"
      class="w-full rounded-md border bg-white px-4 py-2 text-gray-800 flex items-center gap-2">
      <span class="w-6 h-6 rounded-full border flex items-center justify-center text-xl leading-none">+</span>
      Tambah Penyusun
    </button>
  </div>

  {{-- ✅ INI YANG KURANG DARI TADI --}}
  @error('compiler_names')
<p class="text-xs text-red-600 mt-2">{{ $message }}</p>
@enderror
@error('compiler_names.0')
<p class="text-xs text-red-600 mt-2">{{ $message }}</p>
@enderror
</div>

          <!-- RBB USERS -->
          <div class="bg-white rounded-lg shadow-sm p-6">
            <p class="text-xs font-semibold">
              Program Kerja & Anggaran di RBB Divisi Users
              <span class="text-red-500">*</span>
            </p>

            <label class="block text-[11px] mt-2">Kode RBB</label>
            <input type="text" name="rbb_users[kode]"
              class="rbb-code w-full border px-4 py-2.5 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-[#407BFF]"
              placeholder="0.0.0.0" inputmode="numeric" value="{{ old('rbb_users.kode') }}">
              @error('rbb_users.kode')<p class="text-xs text-red-600">{{ $message }}</p>@enderror


            <label class="block text-[11px] mt-3">Nama Program Kerja</label>
            <input type="text" name="rbb_users[nama]"
              class="w-full border px-4 py-2.5 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-[#407BFF]"
              value="{{ old('rbb_users.nama') }}">
              @error('rbb_users.nama')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
            
            <label class="block text-[11px] mt-3">Anggaran</label>
            <input type="number" name="rbb_users[anggaran]"
              class="w-full border px-4 py-2.5 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-[#407BFF]"
              value="{{ old('rbb_users.anggaran') }}">
              @error('rbb_users.anggaran')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
          </div>

          <!-- IT TECH -->
          <div class="bg-white rounded-lg shadow-sm p-6">
            <p class="text-xs font-semibold">
              Program Kerja & Anggaran di RBB Divisi Information Technology
              <span class="text-red-500">*</span>
            </p>

            <label class="block text-[11px] mt-2">Kode RBB</label>
            <input type="text" name="rbb_it[kode]"
              class="rbb-code w-full border px-4 py-2.5 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-[#407BFF]"
              placeholder="0.0.0.0" inputmode="numeric" value="{{ old('rbb_it.kode') }}">
              @error('rbb_it.kode')<p class="text-xs text-red-600">{{ $message }}</p>@enderror  

            <label class="block text-[11px] mt-3">Nama Program Kerja</label>
            <input type="text" name="rbb_it[nama]"
              class="w-full border px-4 py-2.5 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-[#407BFF]"
              value="{{ old('rbb_it.nama') }}">
            @error('rbb_it.nama')<p class="text-xs text-red-600">{{ $message }}</p>@enderror

            <label class="block text-[11px] mt-3">Bundling Anggaran</label>
            <input type="number" name="rbb_it[bundling_anggaran]"
              class="w-full border px-4 py-2.5 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-[#407BFF]"
              value="{{ old('rbb_it.bundling_anggaran') }}">
                @error('rbb_it.bundling')<p class="text-xs text-red-600">{{ $message }}</p>@enderror

            <label class="block text-[11px] mt-3">Anggaran</label>
            <input type="number" name="rbb_it[anggaran]"
              class="w-full border px-4 py-2.5 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-[#407BFF]"
              value="{{ old('rbb_it.anggaran') }}">
                @error('rbb_it.kode')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

          <!-- FOOTER SIGNATURE -->
          <div class="bg-white rounded-lg shadow-sm p-6 text-center text-[11px] text-gray-500">
            <p>Bandung, Juni 2025</p>
            <p>Diajukan oleh</p>
            <p class="font-semibold">Group Head Utama Pengadaan</p>
            <div class="w-64 h-32 mx-auto bg-[#BFD2FF] rounded-md mt-2"></div>
          </div>

          <!-- SUBMIT BUTTON -->
          <div class="flex justify-center">
            <button type="submit"
              class="px-8 py-2 bg-[#407BFF] text-white rounded-md text-sm font-medium hover:bg-[#2d5dd8]">
              Submit
            </button>
          </div>
        </form>
      </div>
    </main>

   <!-- ================= POPUP KONFIRMASI ================= -->
<div id="confirmPopup" class="hidden fixed inset-0 bg-black/30 flex items-center justify-center z-50">
  <div class="bg-white rounded-xl shadow-lg px-8 py-6 text-center">
    <p class="text-sm font-medium mb-4">
      Are You Sure You Want to submit This Project?
    </p>
    <div class="flex justify-center gap-4">
      <button
  type="button"
  id="confirmYes"
  class="px-4 py-1 bg-[#407BFF] text-white rounded-md hover:bg-[#2d5dd8]">
  Yes
</button>
      <button
        type="button"
        id="confirmNo"
        class="px-4 py-1 border rounded-md hover:bg-gray-100">
        No
      </button>
    </div>
  </div>
</div>

    <!-- SCRIPT -->
    <script>
      const candidates = ['Anggie Septian', 'Nama', 'Nama'];
      const compilerWrapper = document.getElementById('compilerWrapper');
      const addBtn = document.getElementById('addRow');

      function createCombobox() {
        const wrap = document.createElement('div');
        wrap.className = 'space-y-1';

        const box = document.createElement('div');
        box.className = 'rounded-md border bg-white px-4 py-2 text-gray-800 flex items-center justify-between ring-1 ring-indigo-500';

        const input = document.createElement('input');
        input.type = 'text';
        input.placeholder = 'Anggie Septian';
        input.className = 'flex-1 outline-none bg-transparent';

        const chevron = document.createElement('button');
        chevron.type = 'button';
        chevron.textContent = '▴';
        chevron.className = 'ml-2 text-gray-600';

        box.appendChild(input);
        box.appendChild(chevron);

        const list = document.createElement('div');
        list.className = 'rounded-md border bg-white shadow max-h-64 overflow-auto';

        function render(filter) {
          list.innerHTML = '';
          candidates
            .filter(n => !filter || n.toLowerCase().includes(filter.toLowerCase()))
            .forEach((n, i) => {
              const btn = document.createElement('button');
              btn.type = 'button';
              btn.className = 'w-full text-left px-4 py-2';
              if (i === 0) btn.className += ' bg-indigo-500 text-white';
              btn.textContent = n;
              btn.addEventListener('click', () => select(n));
              list.appendChild(btn);
            });
        }

        function select(name) {
          const row = document.createElement('div');
          row.className = 'rounded-md border bg-white px-4 py-2 text-gray-800';
          row.textContent = name;

          const hidden = document.createElement('input');
          hidden.type = 'hidden';
          hidden.name = 'compiler_names[]';
          hidden.value = name;

          compilerWrapper.replaceChild(row, wrap);
          addBtn.parentNode.insertBefore(hidden, addBtn);
        }

        input.addEventListener('input', () => render(input.value));
        chevron.addEventListener('click', () => list.classList.toggle('hidden'));

        render('');
        wrap.appendChild(box);
        wrap.appendChild(list);
        return wrap;
      }

      if (addBtn) {
        addBtn.addEventListener('click', () => {
          const combo = createCombobox();
          addBtn.parentNode.insertBefore(combo, addBtn);
        });
      }

      function formatRbb(value) {
        const digits = value.replace(/\D/g, "").slice(0, 4);
        return digits.split("").join(".");
      }

      document.querySelectorAll(".rbb-code").forEach((input) => {
        input.addEventListener("input", (e) => {
          const cursorPos = e.target.selectionStart;
          const oldLength = e.target.value.length;

          e.target.value = formatRbb(e.target.value);

          const newLength = e.target.value.length;
          e.target.selectionEnd = cursorPos + (newLength - oldLength);
        });
      });
    </script>
    <script>
const projectForm = document.getElementById("projectForm");
const confirmPopup = document.getElementById("confirmPopup");
const confirmYes = document.getElementById("confirmYes");
const confirmNo = document.getElementById("confirmNo");

let isConfirmed = false;

// Submit pertama → tampilkan popup
projectForm.addEventListener("submit", function (e) {
  if (!isConfirmed) {
    e.preventDefault();
    confirmPopup.classList.remove("hidden");
  }
});

// Klik YES → submit asli (INI KUNCI UTAMA)
confirmYes.addEventListener("click", function () {
  isConfirmed = true;
  confirmPopup.classList.add("hidden");

  // 🔥 SUBMIT PAKSA, TANPA EVENT LAGI
  projectForm.submit();
});

// Klik NO
confirmNo.addEventListener("click", function () {
  confirmPopup.classList.add("hidden");
  isConfirmed = false;
});
</script>
  </body>
</html>
@endsection