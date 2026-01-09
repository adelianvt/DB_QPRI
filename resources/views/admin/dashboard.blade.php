<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard | Q-PRI</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap');
    body { font-family: 'Inter', sans-serif; }
    .hover-lift { transition: transform 200ms ease-in, background-color 200ms ease-in; }
    .hover-lift:hover { transform: translateY(-2px); }
  </style>
</head>
<body class="bg-gray-50">

@include('components.sidebar', ['active' => 'dashboard'])

<div class="ml-52 min-h-screen">
  @include('components.navbar', ['searchAction' => route('dashboard')])

  <main class="p-8">

    {{-- Summary Cards --}}
    <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <div class="bg-white rounded-lg p-6 border border-gray-200 hover-lift">
        <div class="flex items-center gap-4">
          <div class="w-12 h-12 rounded-lg bg-indigo-100 flex items-center justify-center">
            <span class="text-indigo-600 text-xl font-bold">📁</span>
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
            <span class="text-green-600 text-xl font-bold">✅</span>
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
            <span class="text-yellow-600 text-xl font-bold">⏳</span>
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
            <span class="text-red-600 text-xl font-bold">❌</span>
          </div>
          <div>
            <p class="text-3xl font-semibold text-red-600">{{ $rejected ?? 0 }}</p>
            <p class="text-sm text-gray-600">Rejected</p>
          </div>
        </div>
      </div>
    </section>

    {{-- Table terbaru --}}
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
            @forelse(($pengajuans ?? []) as $p)
              @php
                $code = $p->status?->code;
                $badge = match($code) {
                  'approved'  => 'border-green-600 text-green-600 bg-green-50',
                  'rejected'  => 'border-red-600 text-red-600 bg-red-50',
                  'submitted' => 'border-yellow-600 text-yellow-600 bg-yellow-50',
                  'draft'     => 'border-gray-400 text-gray-600 bg-gray-50',
                  default     => 'border-blue-600 text-blue-600 bg-blue-50',
                };
              @endphp
              <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 text-sm text-gray-900">{{ $p->id }}</td>
                <td class="px-6 py-4 text-sm text-gray-900">{{ $p->judul }}</td>
                <td class="px-6 py-4 text-sm text-gray-900">{{ $p->maker?->name ?? '-' }}</td>
                <td class="px-6 py-4 text-sm text-gray-900">{{ data_get($p->meta,'tipe','-') }}</td>
                <td class="px-6 py-4 text-sm text-gray-900">{{ optional($p->created_at)->format('Y-m-d') }}</td>
                <td class="px-6 py-4">
                  <span class="inline-flex items-center justify-center w-56 px-4 py-1.5 rounded-full text-xs font-medium border {{ $badge }}">
                    {{ $p->status?->label ?? '-' }}
                  </span>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">
                  Tidak ada data.
                </td>
              </tr>
            @endforelse
          </tbody>

        </table>
      </div>
    </section>

    @if(isset($pengajuans) && method_exists($pengajuans,'links'))
      <div class="mt-6">
        {{ $pengajuans->links() }}
      </div>
    @endif

  </main>
</div>

</body>
</html>
