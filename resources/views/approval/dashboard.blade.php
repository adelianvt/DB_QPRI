<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Approver Dashboard | Q-PRI</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap');
    body { font-family: 'Inter', sans-serif; }
    .hover-lift { transition: transform 200ms ease-in, background-color 200ms ease-in; }
    .hover-lift:hover { transform: translateY(-2px); }
  </style>
</head>

<body class="bg-gray-50">

  @include('components.sidebar')

  <div class="ml-52 min-h-screen">
    @include('components.navbar', ['searchAction' => route('dashboard')])

    <main class="p-8">

      @if(session('success'))
        <div class="mb-6 px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-green-700">
          {{ session('success') }}
        </div>
      @endif

      {{-- Summary Cards --}}
      <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg p-6 border border-gray-200 hover-lift">
          <p class="text-3xl font-semibold text-indigo-600">{{ $total ?? 0 }}</p>
          <p class="text-sm text-gray-600">Total Project</p>
        </div>

        <div class="bg-white rounded-lg p-6 border border-gray-200 hover-lift">
          <p class="text-3xl font-semibold text-green-600">{{ $approvedCount ?? 0 }}</p>
          <p class="text-sm text-gray-600">Approved</p>
        </div>

        <div class="bg-white rounded-lg p-6 border border-gray-200 hover-lift">
          <p class="text-3xl font-semibold text-yellow-600">{{ $waitingCount ?? 0 }}</p>
          <p class="text-sm text-gray-600">Waiting For Approval</p>
        </div>

        <div class="bg-white rounded-lg p-6 border border-gray-200 hover-lift">
          <p class="text-3xl font-semibold text-red-600">{{ $rejectedCount ?? 0 }}</p>
          <p class="text-sm text-gray-600">Rejected</p>
        </div>
      </section>

      <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold text-gray-800">Daftar Pengajuan</h2>
      </div>

      {{-- Table --}}
      <section class="bg-white rounded-lg overflow-hidden border border-gray-200 mb-8">
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-indigo-600">
              <tr>
                <th class="px-6 py-4 text-left text-sm font-medium text-white">ID</th>
                <th class="px-6 py-4 text-left text-sm font-medium text-white">Nomor</th>
                <th class="px-6 py-4 text-left text-sm font-medium text-white">Name</th>
                <th class="px-6 py-4 text-left text-sm font-medium text-white">Pemilik</th>
                <th class="px-6 py-4 text-left text-sm font-medium text-white">Type</th>
                <th class="px-6 py-4 text-left text-sm font-medium text-white">Start Date</th>
                <th class="px-6 py-4 text-left text-sm font-medium text-white">End Date</th>
                <th class="px-6 py-4 text-left text-sm font-medium text-white">Status</th>
                <th class="px-6 py-4 text-left text-sm font-medium text-white">Action</th>
              </tr>
            </thead>

            <tbody class="divide-y divide-gray-200">
              @forelse($items as $p)
                @php
                  $code = $p->status->code ?? null;
                  $label = $p->status->label ?? $p->status->name ?? ($code ? strtoupper($code) : '-');

                  $badge = match($code) {
                    'approved'  => 'border-green-600 text-green-600 bg-green-50',
                    'rejected'  => 'border-red-600 text-red-600 bg-red-50',
                    'submitted' => 'border-yellow-600 text-yellow-600 bg-yellow-50',
                    'draft'     => 'border-gray-400 text-gray-600 bg-gray-50',
                    default     => 'border-blue-600 text-blue-600 bg-blue-50',
                  };

                  $tipe = data_get($p->meta,'tipe','-');
                  $tipeLabel = match($tipe) {
                    'pengembangan' => 'Pengembangan Internal',
                    'internal'     => 'Pengembangan Internal',
                    'pengadaan'    => 'Pengadaan',
                    default        => $tipe,
                  };
                @endphp

                <tr class="hover:bg-gray-50">
                  <td class="px-6 py-4 text-sm text-gray-900">{{ $p->id }}</td>
                  <td class="px-6 py-4 text-sm text-gray-900">{{ $p->nomor_pengajuan ?? '-' }}</td>
                  <td class="px-6 py-4 text-sm text-gray-900">{{ $p->judul ?? '-' }}</td>
                  <td class="px-6 py-4 text-sm text-gray-900">{{ $p->maker?->name ?? '-' }}</td>
                  <td class="px-6 py-4 text-sm text-gray-900">{{ $tipeLabel }}</td>
                  <td class="px-6 py-4 text-sm text-gray-900">{{ $p->tanggal_mulai ?? '-' }}</td>
                  <td class="px-6 py-4 text-sm text-gray-900">{{ $p->tanggal_selesai ?? '-' }}</td>

                  <td class="px-6 py-4">
                    <span class="inline-flex items-center justify-center w-56 px-4 py-1.5 rounded-full text-xs font-medium border {{ $badge }}">
                      {{ $label }}
                    </span>
                  </td>

                  <td class="px-6 py-4">
                    <a href="{{ route('approvals.show', $p->id) }}"
                       class="text-indigo-600 hover:underline text-sm">
                      View
                    </a>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="9" class="px-6 py-6 text-sm text-gray-500 text-center">
                    Data pengajuan belum ada.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </section>

      @if(method_exists($items, 'links'))
        <div class="mt-4">
          {{ $items->links() }}
        </div>
      @endif

    </main>
  </div>

</body>
</html>
