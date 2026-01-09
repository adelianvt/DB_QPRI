<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Approval - Approver | Q-PRI</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body { font-family: 'Inter', sans-serif; }
  </style>
</head>
<body class="bg-gray-50">

@include('components.sidebar')

<div class="ml-52 min-h-screen">
  @include('components.navbar', ['searchAction' => route('approvals.index')])

  <main class="p-8 pt-6">

    <h1 class="text-xl font-semibold text-gray-900 mb-6">Daftar Approval</h1>

    <section class="bg-white rounded-lg overflow-hidden border border-gray-200">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-indigo-600">
            <tr>
              <th class="px-6 py-4 text-left text-sm font-medium text-white">ID</th>
              <th class="px-6 py-4 text-left text-sm font-medium text-white">Name</th>
              <th class="px-6 py-4 text-left text-sm font-medium text-white">Pemilik</th>
              <th class="px-6 py-4 text-left text-sm font-medium text-white">Type</th>
              <th class="px-6 py-4 text-left text-sm font-medium text-white">Status</th>
              <th class="px-6 py-4 text-left text-sm font-medium text-white">Action</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-gray-200">
            @forelse($pengajuans as $p)
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

                <td class="px-6 py-4">
                  <span class="inline-flex items-center justify-center w-56 px-4 py-1.5 rounded-full text-xs font-medium border {{ $badge }}">
                    {{ $p->status?->label ?? '-' }}
                  </span>
                </td>

                <td class="px-6 py-4">
                  <x-pengajuan-actions :pengajuan="$p" role="APPROVER" />
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">
                  Tidak ada data approval.
                </td>
              </tr>
            @endforelse
          </tbody>

        </table>
      </div>
    </section>

    <div class="mt-6">
      {{ $pengajuans->links() }}
    </div>

  </main>
</div>

@include('components.dropdown-script')

</body>
</html>