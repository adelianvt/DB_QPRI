@extends('layouts.app')

@section('content')
<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <title>Formulir Registrasi Project</title>
    <script src="https://cdn.tailwindcss.com"></script>
  </head>

  @php
    $p = $pengajuan;
    $meta = (array) ($p->meta ?? []);

    // value utama
    $judul = old('judul', $p->judul ?? '');
    $deskripsi = old('deskripsi', $p->deskripsi ?? '');

    // meta
    $divisi = old('divisi', data_get($meta, 'divisi', ''));
    $tipe   = old('tipe', data_get($meta, 'tipe', ''));

    $group1 = old('group_utama', data_get($meta, 'group_utama', [])) ?: [];
    $group2 = old('group_utama_2', data_get($meta, 'group_utama_2', [])) ?: [];

    $oldComp = old('compiler_names', data_get($meta, 'compiler_names', [])) ?: [];

    $rbbUsers = (array) old('rbb_users', data_get($meta,'rbb_users', []));
    $rbbIt    = (array) old('rbb_it', data_get($meta,'rbb_it', []));

    $groups = [
      'IT Architecture & Governance',
      'Corporate Service',
      'Application Management Management',
      'Digital Business',
      'Business Intelligence, Analytics & Regulatory',
      'Backend Management',
      'Helpdesk & Support',
      'Operation Management DC',
      'IT Security',
    ];

    // ✅ Watermark Contact Person (sama seperti create kamu)
    $wmName  = 'Sani Hardiansa';
    $wmPhone = '+62 812-2005-343';
    $wmEmail = 'shardiansa@BANKBJB.CO.ID';
  @endphp

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

        @if ($errors->any())
          <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-700 text-sm">
            {{ $errors->first() }}
          </div>
        @endif

        <!-- FORM START -->
        <form id="projectForm" class="space-y-6" method="POST" action="{{ route('pengajuans.update', $p->id) }}">
          @csrf
          @method('PUT')

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
              value="{{ $judul }}"
              class="w-full border px-4 py-2.5 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-[#407BFF]"
              placeholder="Nama Project"
            >
            @error('judul')
              <p class="text-xs text-red-600 mt-2">{{ $message }}</p>
            @enderror
          </div>

          <!-- ✅ Deskripsi (ditampilin, sama kayak create) -->
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
            >{{ $deskripsi }}</textarea>
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
              <option value="Divisi Human Capital" {{ $divisi=='Divisi Human Capital'?'selected':'' }}>Divisi Human Capital</option>
              <option value="Divisi 1" {{ $divisi=='Divisi 1'?'selected':'' }}>Divisi 1</option>
              <option value="Divisi 2" {{ $divisi=='Divisi 2'?'selected':'' }}>Divisi 2</option>
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
                  {{ $tipe==='internal'?'checked':'' }}>
                <span>Pengembangan Internal</span>
              </label>
              <label class="flex gap-2 items-center">
                <input type="radio" name="tipe" value="pengadaan" class="w-4 h-4 text-[#407BFF]" required
                  {{ $tipe==='pengadaan'?'checked':'' }}>
                <span>Pengadaan</span>
              </label>
            </div>
            @error('tipe')
              <p class="text-xs text-red-600 mt-2">{{ $message }}</p>
            @enderror
          </div>

          <!-- Grup Utama 1 -->
          <div class="bg-white rounded-lg shadow-sm p-6">
            <p class="text-xs font-semibold">
              Group Utama Pengembangan/Pengadaan <span class="text-red-500">*</span>
            </p>
            <div class="mt-1 border-2 border-dashed border-[#BFD2FF] px-4 py-3 rounded-lg space-y-1 text-sm bg-white">
              @foreach($groups as $opt)
                <label class="flex gap-2">
                  <input type="checkbox" name="group_utama[]" value="{{ $opt }}" class="mt-0.5"
                    {{ in_array($opt, $group1) ? 'checked' : '' }}>
                  {{ $opt }}
                </label>
              @endforeach
            </div>
          </div>

          <!-- Contact Person (watermark) -->
          <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-sm font-medium text-gray-700 mb-4">
              Contact Person (Grup User/Nama Group Penanggungjawab Pekerjaan) <span class="text-red-500">*</span>
            </h3>

            <div class="space-y-4">
              <div>
                <label for="contactName" class="block text-xs font-medium text-gray-600 mb-2">Nama</label>
                <input type="text" id="contactName" readonly
                  value="{{ $wmName }}"
                  class="w-full px-3 py-2.5 border border-gray-300 rounded-md text-sm text-gray-900 bg-gray-50">
                <input type="hidden" name="contact[name]" value="{{ $wmName }}">
              </div>

              <div>
                <label for="contactPhone" class="block text-xs font-medium text-gray-600 mb-2">No Handphone</label>
                <input type="tel" id="contactPhone" readonly
                  value="{{ $wmPhone }}"
                  class="w-full px-3 py-2.5 border border-gray-300 rounded-md text-sm text-gray-900 bg-gray-50">
                <input type="hidden" name="contact[phone]" value="{{ $wmPhone }}">
              </div>

              <div>
                <label for="contactEmail" class="block text-xs font-medium text-gray-600 mb-2">Email</label>
                <input type="email" id="contactEmail" readonly
                  value="{{ $wmEmail }}"
                  class="w-full px-3 py-2.5 border border-gray-300 rounded-md text-sm text-gray-900 bg-gray-50">
                <input type="hidden" name="contact[email]" value="{{ $wmEmail }}">
              </div>
            </div>
          </div>

          <!-- Grup Utama 2 -->
          <div class="bg-white rounded-lg shadow-sm p-6">
            <p class="text-xs font-semibold">
              Group Utama Pengembangan/Pengadaan <span class="text-red-500">*</span>
            </p>
            <div class="mt-1 border-2 border-dashed border-[#BFD2FF] px-4 py-3 rounded-lg space-y-1 text-sm bg-white">
              @foreach($groups as $opt)
                <label class="flex gap-2">
                  <input type="checkbox" name="group_utama_2[]" value="{{ $opt }}" class="mt-0.5"
                    {{ in_array($opt, $group2) ? 'checked' : '' }}>
                  {{ $opt }}
                </label>
              @endforeach
            </div>
          </div>

          <!-- Penyusun -->
          <div class="bg-white rounded-lg shadow-sm p-6">
            <p class="text-xs font-semibold">
              Nama Lengkap Penyusun Dokumen Pengembangan/Pengadaan
              <span class="text-red-500">*</span>
            </p>

            <div id="compilerWrapper" class="space-y-2">
              <div id="compilerForm" class="space-y-2"></div>

              @foreach($oldComp as $nm)
                <input type="hidden" name="compiler_names[]" value="{{ $nm }}">
                <div class="rounded-md border bg-white px-4 py-2 text-gray-800">{{ $nm }}</div>
              @endforeach

              <button id="addRow" type="button"
                      class="w-full rounded-md border bg-white px-4 py-2 text-gray-800 flex items-center gap-2">
                <span class="w-6 h-6 rounded-full border flex items-center justify-center text-xl leading-none">+</span>
                Tambah Penyusun
              </button>
            </div>
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
              placeholder="0.0.0.0" inputmode="numeric" value="{{ $rbbUsers['kode'] ?? '' }}">

            <label class="block text-[11px] mt-3">Nama Program Kerja</label>
            <input type="text" name="rbb_users[nama]"
              class="w-full border px-4 py-2.5 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-[#407BFF]"
              value="{{ $rbbUsers['nama'] ?? '' }}">

            <label class="block text-[11px] mt-3">Anggaran</label>
            <input type="number" name="rbb_users[anggaran]"
              class="w-full border px-4 py-2.5 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-[#407BFF]"
              value="{{ $rbbUsers['anggaran'] ?? '' }}">
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
              placeholder="0.0.0.0" inputmode="numeric" value="{{ $rbbIt['kode'] ?? '' }}">

            <label class="block text-[11px] mt-3">Nama Program Kerja</label>
            <input type="text" name="rbb_it[nama]"
              class="w-full border px-4 py-2.5 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-[#407BFF]"
              value="{{ $rbbIt['nama'] ?? '' }}">

            <label class="block text-[11px] mt-3">Bundling Anggaran</label>
            <input type="number" name="rbb_it[bundling]"
              class="w-full border px-4 py-2.5 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-[#407BFF]"
              value="{{ $rbbIt['bundling'] ?? '' }}">

            <label class="block text-[11px] mt-3">Anggaran</label>
            <input type="number" name="rbb_it[anggaran]"
              class="w-full border px-4 py-2.5 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-[#407BFF]"
              value="{{ $rbbIt['anggaran'] ?? '' }}">
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

    <!-- POPUP KONFIRMASI (sebelum submit) -->
    <div id="confirmPopup" class="hidden fixed inset-0 bg-black/30 flex items-center justify-center z-50">
      <div class="bg-white rounded-xl shadow-lg px-8 py-6 text-center">
        <p class="text-sm font-medium mb-4">
          Are You Sure You Want to submit This Project?
        </p>
        <div class="flex justify-center gap-4">
          <button id="confirmYes" type="button"
            class="px-4 py-1 bg-[#407BFF] text-white rounded-md text-sm hover:bg-[#2d5dd8]">
            Yes
          </button>
          <button id="confirmNo" type="button"
            class="px-4 py-1 border rounded-md text-sm hover:bg-gray-100">
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
        const digits = value.replace(/\\D/g, "").slice(0, 4);
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

      const projectForm = document.getElementById("projectForm");
      const confirmPopup = document.getElementById("confirmPopup");
      let isConfirmed = false;

      if (projectForm) {
        projectForm.addEventListener("submit", (e) => {
          if (!isConfirmed) {
            e.preventDefault();
            confirmPopup.classList.remove("hidden");
          }
        });
      }

      const confirmYes = document.getElementById("confirmYes");
      const confirmNo = document.getElementById("confirmNo");

      if (confirmYes) {
        confirmYes.addEventListener("click", () => {
          isConfirmed = true;
          confirmPopup.classList.add("hidden");
          projectForm.submit();
        });
      }

      if (confirmNo) {
        confirmNo.addEventListener("click", () => {
          isConfirmed = false;
          confirmPopup.classList.add("hidden");
        });
      }
    </script>
  </body>
</html>
@endsection