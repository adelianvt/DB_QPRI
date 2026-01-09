@php
  $readonly = $readonly ?? false;
  $p = $p ?? null;
  $meta = (array) ($meta ?? []);

  $judul = old('judul', $p->judul ?? '');
  $divisi = old('divisi', data_get($meta,'divisi',''));
  $tipe = old('tipe', data_get($meta,'tipe',''));

  $group1 = old('group_utama', data_get($meta,'group_utama', [])) ?: [];
  $group2 = old('group_utama_2', data_get($meta,'group_utama_2', [])) ?: [];

  $contact = data_get($meta,'contact', []);
@endphp

<div class="bg-[#F5F7FB] space-y-6">

  {{-- Nama Project --}}
  <div class="bg-white rounded-lg shadow-sm p-6">
    <label class="block text-xs font-semibold">
      Pengajuan Nama Project <span class="text-red-500">*</span>
    </label>
    <input
      name="judul"
      value="{{ $judul }}"
      {{ $readonly ? 'readonly' : '' }}
      class="w-full border px-4 py-2.5 rounded-md text-sm
      {{ $readonly ? 'bg-gray-100 cursor-not-allowed' : '' }}"
      placeholder="Nama Project"
    >
  </div>

  {{-- Divisi --}}
  <div class="bg-white rounded-lg shadow-sm p-6">
    <label class="block text-xs font-semibold">
      Divisi Yang Menginisiasikan <span class="text-red-500">*</span>
    </label>
    <select
      name="divisi"
      {{ $readonly ? 'disabled' : '' }}
      class="w-full border px-4 py-2.5 rounded-md text-sm"
    >
      <option value="">Pilih Divisi</option>
      <option {{ $divisi=='Divisi Human Capital'?'selected':'' }}>Divisi Human Capital</option>
      <option {{ $divisi=='Divisi 1'?'selected':'' }}>Divisi 1</option>
      <option {{ $divisi=='Divisi 2'?'selected':'' }}>Divisi 2</option>
    </select>
  </div>

  {{-- Tipe Project --}}
  <div class="bg-white rounded-lg shadow-sm p-6">
    <p class="text-xs font-semibold">
      Tipe Project <span class="text-red-500">*</span>
    </p>
    <div class="mt-1 space-y-1 text-sm">
      <label class="flex gap-2 items-center">
        <input type="radio" name="tipe" value="internal"
          {{ $tipe==='internal'?'checked':'' }}
          {{ $readonly?'disabled':'' }}>
        <span>Pengembangan Internal</span>
      </label>
      <label class="flex gap-2 items-center">
        <input type="radio" name="tipe" value="pengadaan"
          {{ $tipe==='pengadaan'?'checked':'' }}
          {{ $readonly?'disabled':'' }}>
        <span>Pengadaan</span>
      </label>
    </div>
  </div>

  {{-- Group Utama 1 --}}
  @php
    $groups = [
      'IT Architecture & Governance',
      'Corporate Service',
      'Application Management Management',
      'Digital Business',
      'Business Intelligence, Analytics & Regulatory',
      'Backend Management',
      'Helpdesk & Support',
      'Operation Management DC',
      'IT Security'
    ];
  @endphp

  <div class="bg-white rounded-lg shadow-sm p-6">
    <p class="text-xs font-semibold">
      Group Utama Pengembangan/Pengadaan <span class="text-red-500">*</span>
    </p>
    <div class="mt-1 border-2 border-dashed border-[#BFD2FF] px-4 py-3 rounded-lg space-y-1 text-sm bg-white">
      @foreach($groups as $g)
        <label class="flex gap-2">
          <input type="checkbox" name="group_utama[]"
            value="{{ $g }}"
            {{ in_array($g,$group1)?'checked':'' }}
            {{ $readonly?'disabled':'' }}>
          {{ $g }}
        </label>
      @endforeach
    </div>
  </div>

  {{-- Contact Person --}}
  <div class="bg-white rounded-lg shadow-sm p-6">
    <h3 class="text-sm font-medium text-gray-700 mb-4">
      Contact Person
    </h3>

    <input readonly value="{{ $contact['name'] ?? '' }}"
      class="w-full mb-2 border px-3 py-2.5 rounded bg-gray-50">
    <input readonly value="{{ $contact['phone'] ?? '' }}"
      class="w-full mb-2 border px-3 py-2.5 rounded bg-gray-50">
    <input readonly value="{{ $contact['email'] ?? '' }}"
      class="w-full border px-3 py-2.5 rounded bg-gray-50">
  </div>

</div>