@extends('layouts.app')

@section('content')
@php
  $user = auth()->user();
 $roleId = (int) data_get($user, 'role_id', 0);



  $meta  = (array)($pengajuan->meta ?? []);
  $steps = (array) data_get($meta, 'decision.steps', []);

  // last actor for approve/reject
  $lastApproveBy = null;
  $lastRejectBy  = null;
  foreach (array_reverse($steps) as $st) {
    if (!$lastApproveBy && ($st['action'] ?? '') === 'approve') $lastApproveBy = ($st['by_role'] ?? null);
    if (!$lastRejectBy  && ($st['action'] ?? '') === 'reject')  $lastRejectBy  = ($st['by_role'] ?? null);
    if ($lastApproveBy && $lastRejectBy) break;
  }

  $contactName  = data_get($meta, 'contact.nama');
  $contactPhone = data_get($meta, 'contact.hp');
  $contactEmail = data_get($meta, 'contact.email');

  $g1 = data_get($meta,'group_utama', []);
  $g2 = data_get($meta,'group_utama_2', []);

  // fallback kalau ternyata kebawa string
  if (is_string($g1)) $g1 = array_filter(array_map('trim', explode(',', $g1)));
  if (is_string($g2)) $g2 = array_filter(array_map('trim', explode(',', $g2)));

  if (!is_array($g1)) $g1 = [];
  if (!is_array($g2)) $g2 = [];

  $compiler = (array) data_get($meta,'compiler_names', []);

  $rbbUsers = (array) data_get($meta,'rbb_users', []);
  $rbbIt    = (array) data_get($meta,'rbb_it', []);

  $code = strtolower($pengajuan->status?->code ?? '');

  // ✅ status label by role
  $statusLabel = match($code) {
    'pending_approver1' => 'Waiting by GH CRV',
    'pending_iag'       => 'Waiting by IAG',
    'pending_approver2' => 'Waiting by GH IAG',
    'approved'          => 'Approved' . ($lastApproveBy ? ' by '.$lastApproveBy : ''),
    'rejected'          => 'Rejected' . ($lastRejectBy ? ' by '.$lastRejectBy : ''),
    default             => $pengajuan->status?->label ?? '-',
  };

  // ✅ tombol approver tampil sesuai tahap
  $canApprove = ($code === 'pending_approver1' && $roleId === 2)
             || ($code === 'pending_approver2' && $roleId === 14);

  $canReject  = $canApprove;

  // ==============================
  // ✅ FORM TAMBAHAN IAG (ambil dari meta)
  // ==============================
  $iag = data_get($meta, 'iag', []);
  if(!is_array($iag)) $iag = [];

  $iagKode  = data_get($iag, 'kode_project');
  $iagNama  = data_get($iag, 'nama_project');
$itagList = (array) data_get($iag, 'itag_list', []);
$itwList  = (array) data_get($iag, 'itw_list', []);
$karList  = (array) data_get($iag, 'karakter', []);
  $hasIagForm = !empty($iagKode) || !empty($iagNama) || count($itagList) || count($itwList) || count($karList);
@endphp

<div class="bg-white border border-gray-200 rounded-lg p-6">
  <div class="flex items-start justify-between gap-4">
    <div>
      <h1 class="text-xl font-semibold text-gray-900">Detail Pengajuan #{{ $pengajuan->id }}</h1>
      <p class="text-sm text-gray-500 mt-1">{{ $pengajuan->judul }}</p>
    </div>

    <div class="text-sm text-gray-600">
      <div>Status: <span class="font-semibold">{{ $statusLabel }}</span></div>
      <div>Pemilik: <span class="font-semibold">{{ $pengajuan->maker?->name ?? '-' }}</span></div>
    </div>
  </div>

  <hr class="my-6">

  <div class="space-y-6 text-sm">

    {{-- ===================== --}}
    {{-- DATA PROJECT --}}
    {{-- ===================== --}}
    <div>
      <div class="font-semibold text-gray-700 mb-1">Pengajuan Nama Project</div>
      <div class="border rounded-lg px-4 py-3 bg-gray-50">{{ $pengajuan->judul ?? '-' }}</div>
    </div>

    <div>
      <div class="font-semibold text-gray-700 mb-1">Deskripsi</div>
      <div class="border rounded-lg px-4 py-3 bg-gray-50 whitespace-pre-wrap">{{ $pengajuan->deskripsi ?? '-' }}</div>
    </div>

    <div>
      <div class="font-semibold text-gray-700 mb-1">Divisi Yang Menginisiasikan</div>
      <div class="border rounded-lg px-4 py-3 bg-gray-50">{{ data_get($meta,'divisi','-') }}</div>
    </div>

    <div>
      <div class="font-semibold text-gray-700 mb-1">Tipe Project</div>
      <div class="border rounded-lg px-4 py-3 bg-gray-50">{{ data_get($meta,'tipe','-') }}</div>
    </div>

    {{-- ===================== --}}
    {{-- GROUP / CONTACT / PENYUSUN --}}
    {{-- ===================== --}}
    <div>
      <div class="font-semibold text-gray-700 mb-2">Group Utama Pengembangan/Pengadaan</div>
      <div class="border rounded-lg px-4 py-3 bg-gray-50">
        @if(count($g1))
          <ul class="list-disc pl-5 space-y-1">
            @foreach($g1 as $x)<li>{{ $x }}</li>@endforeach
          </ul>
        @else
          -
        @endif
      </div>
    </div>

    <div>
      <div class="font-semibold text-gray-700 mb-2">Contact Person (Grup User/Nama Group Penanggungjawab Pekerjaan)</div>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
        <div class="border rounded-lg px-4 py-3 bg-gray-50">
          <div class="text-xs text-gray-500 mb-1">Nama</div>
          <div class="font-medium">{{ $contactName }}</div>
        </div>
        <div class="border rounded-lg px-4 py-3 bg-gray-50">
          <div class="text-xs text-gray-500 mb-1">No Handphone</div>
          <div class="font-medium">{{ $contactPhone }}</div>
        </div>
        <div class="border rounded-lg px-4 py-3 bg-gray-50">
          <div class="text-xs text-gray-500 mb-1">Email</div>
          <div class="font-medium">{{ $contactEmail }}</div>
        </div>
      </div>
    </div>

    <div>
      <div class="font-semibold text-gray-700 mb-2">Group Utama Pengembangan/Pengadaan (2)</div>
      <div class="border rounded-lg px-4 py-3 bg-gray-50">
        @if(count($g2))
          <ul class="list-disc pl-5 space-y-1">
            @foreach($g2 as $x)<li>{{ $x }}</li>@endforeach
          </ul>
        @else
          -
        @endif
      </div>
    </div>

    <div>
      <div class="font-semibold text-gray-700 mb-2">Penyusun</div>
      <div class="border rounded-lg px-4 py-3 bg-gray-50">
        @if(count($compiler))
          <ul class="list-disc pl-5 space-y-1">
            @foreach($compiler as $x)<li>{{ $x }}</li>@endforeach
          </ul>
        @else
          -
        @endif
      </div>
    </div>

    {{-- ===================== --}}
    {{-- RBB USERS & IT --}}
    {{-- ===================== --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div class="border rounded-lg overflow-hidden">
        <div class="bg-indigo-600 text-white px-4 py-2 font-semibold">RBB Divisi Users</div>
        <div class="p-4 space-y-2 bg-gray-50">
          <div><span class="font-semibold">Kode:</span> {{ data_get($rbbUsers,'kode','-') }}</div>
          <div><span class="font-semibold">Nama:</span> {{ data_get($rbbUsers,'nama','-') }}</div>
          <div><span class="font-semibold">Anggaran:</span> {{ data_get($rbbUsers,'anggaran','-') }}</div>
        </div>
      </div>

      <div class="border rounded-lg overflow-hidden">
        <div class="bg-indigo-600 text-white px-4 py-2 font-semibold">RBB Divisi IT</div>
        <div class="p-4 space-y-2 bg-gray-50">
          <div><span class="font-semibold">Kode:</span> {{ data_get($rbbIt,'kode','-') }}</div>
          <div><span class="font-semibold">Nama:</span> {{ data_get($rbbIt,'nama','-') }}</div>
          <div><span class="font-semibold">Bundling:</span> {{ data_get($rbbIt,'bundling','-') }}</div>
          <div><span class="font-semibold">Anggaran:</span> {{ data_get($rbbIt,'anggaran','-') }}</div>
        </div>
      </div>
    </div>

    {{-- ===================== --}}
    {{-- ALASAN REJECT --}}
    {{-- ===================== --}}
    @if($pengajuan->rejection_reason)
      <div class="p-4 rounded-lg border border-red-200 bg-red-50 text-sm text-red-700">
        <div class="font-semibold mb-1">Alasan Reject:</div>
        <div>{{ $pengajuan->rejection_reason }}</div>
      </div>
    @endif

    {{-- ===================== --}}
    {{-- ✅ FORM TAMBAHAN IAG PALING BAWAH --}}
    {{-- ===================== --}}
    @if($hasIagForm)
      <div class="border rounded-lg overflow-hidden">
        <div class="bg-indigo-600 text-white px-4 py-2 font-semibold">
          Form Tambahan IAG
        </div>

        <div class="p-4 space-y-4 bg-gray-50">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <div class="font-semibold text-gray-700 mb-1">Kode Project</div>
              <div class="border rounded-lg px-4 py-3 bg-white">{{ $iagKode ?: '-' }}</div>
            </div>
            <div>
              <div class="font-semibold text-gray-700 mb-1">Nama Project</div>
              <div class="border rounded-lg px-4 py-3 bg-white">{{ $iagNama ?: '-' }}</div>
            </div>
          </div>

          <div>
            <div class="font-semibold text-gray-700 mb-2">IT Architecture Governance</div>
            <div class="border rounded-lg px-4 py-3 bg-white">
              @if(count($itagList))
                <ul class="list-disc pl-5 space-y-1">
                  @foreach($itagList as $x)<li>{{ $x }}</li>@endforeach
                </ul>
              @else
                -
              @endif
            </div>
          </div>

          <div>
            <div class="font-semibold text-gray-700 mb-2">IT Technical Writer</div>
            <div class="border rounded-lg px-4 py-3 bg-white">
              @if(count($itwList))
                <ul class="list-disc pl-5 space-y-1">
                  @foreach($itwList as $x)<li>{{ $x }}</li>@endforeach
                </ul>
              @else
                -
              @endif
            </div>
          </div>

          <div>
            <div class="font-semibold text-gray-700 mb-2">Karakteristik</div>
            <div class="border rounded-lg px-4 py-3 bg-white">
              @if(count($karList))
                <ul class="list-disc pl-5 space-y-1">
                  @foreach($karList as $x)<li>{{ $x }}</li>@endforeach
                </ul>
              @else
                -
              @endif
            </div>
          </div>
        </div>
      </div>
    @endif

  </div>

  {{-- ===================== --}}
  {{-- ACTION BUTTONS --}}
  {{-- ===================== --}}
  <div class="mt-8 flex items-center justify-between gap-3">
    <div class="flex items-center gap-3">
      <a href="{{ route('pengajuans.index') }}"
         class="px-4 py-2 rounded-lg border hover:bg-gray-50 text-sm">
        Back
      </a>

      @if($canApprove)
        <form method="POST" action="{{ route('pengajuans.approve', $pengajuan->id) }}"
              onsubmit="return confirm('Apakah anda yakin ingin meng-approve formulir/project ini?')">
          @csrf
          <button type="submit"
                  class="px-4 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700 text-sm">
            Approve
          </button>
        </form>
      @endif

      @if($canReject)
        <button type="button"
                onclick="document.getElementById('rejectBox').classList.toggle('hidden')"
                class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 text-sm">
          Reject
        </button>
      @endif
    </div>

    {{-- ✅ DOWNLOAD BUTTON --}}
    <a href="{{ route('pengajuans.download', $pengajuan->id) }}"
       class="px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 text-sm">
      Download
    </a>
  </div>

  @if($canReject)
    <div id="rejectBox" class="hidden mt-6 border border-gray-200 rounded-lg p-5 bg-gray-50">
      <form method="POST" action="{{ route('pengajuans.reject', $pengajuan->id) }}"
            class="space-y-3"
            onsubmit="return confirm('Apakah anda yakin ingin me-reject formulir/project ini?')">
        @csrf

        <label class="block text-sm font-semibold text-gray-700">
          Alasan Reject <span class="text-red-600">*</span>
        </label>

        <textarea name="rejection_reason" rows="3" required
                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-400"
                  placeholder="Tulis alasan reject...">{{ old('rejection_reason') }}</textarea>

        @error('rejection_reason')
          <div class="text-sm text-red-600">{{ $message }}</div>
        @enderror

        <div class="flex gap-2">
          <button type="submit"
                  class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 text-sm">
            Submit Reject
          </button>
          <button type="button"
                  onclick="document.getElementById('rejectBox').classList.add('hidden')"
                  class="px-4 py-2 rounded-lg border hover:bg-white text-sm">
            Cancel
          </button>
        </div>
      </form>
    </div>
  @endif
</div>
@endsection