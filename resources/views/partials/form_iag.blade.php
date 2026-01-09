@php
  $iag = (array) data_get($meta,'iag',[]);
@endphp

<section class="bg-white rounded-lg shadow-sm p-6 space-y-4">
  <h2 class="text-sm font-semibold">Form Tambahan IAG</h2>

  <div><b>Kode Project:</b> {{ $iag['kode_project'] ?? '-' }}</div>
  <div><b>Nama Project:</b> {{ $iag['nama_project'] ?? '-' }}</div>

  <div>
    <b>Group Pengguna:</b>
    <ul class="list-disc ml-5">
      @forelse(($iag['group_pengguna'] ?? []) as $x)
        <li>{{ $x }}</li>
      @empty <li>-</li> @endforelse
    </ul>
  </div>

  <div>
    <b>IT Terkait:</b>
    <ul class="list-disc ml-5">
      @forelse(($iag['it_terkait'] ?? []) as $x)
        <li>{{ $x }}</li>
      @empty <li>-</li> @endforelse
    </ul>
  </div>
</section>