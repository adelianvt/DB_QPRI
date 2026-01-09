<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Management | Q-PRI</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body { font-family: 'Inter', sans-serif; }
  </style>
</head>
<body class="bg-gray-50">

@include('components.sidebar')

<div class="ml-52 min-h-screen">
  @include('components.navbar')

  <main class="p-8">

    <h1 class="text-xl font-semibold mb-6">All Pengajuan</h1>

    <section class="bg-white rounded-lg border overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-indigo-600">
            <tr>
              <th class="px-6 py-4 text-left text-white">ID</th>
              <th class="px-6 py-4 text-left text-white">Name</th>
              <th class="px-6 py-4 text-left text-white">Owner</th>
              <th class="px-6 py-4 text-left text-white">Type</th>
              <th class="px-6 py-4 text-left text-white">Status</th>
              <th class="px-6 py-4 text-left text-white">Action</th>
            </tr>
          </thead>

          <tbody class="divide-y">
          @foreach($pengajuans as $p)
            @php
              $code = $p->status?->code;
              $badge = match($code) {
                'approved' => 'border-green-600 text-green-600 bg-green-50',
                'rejected' => 'border-red-600 text-red-600 bg-red-50',
                'draft' => 'border-gray-400 text-gray-600 bg-gray-50',
                default => 'border-yellow-600 text-yellow-600 bg-yellow-50',
              };
            @endphp

            <tr class="hover:bg-gray-50">
              <td class="px-6 py-4">{{ $p->id }}</td>
              <td class="px-6 py-4">{{ $p->judul }}</td>
              <td class="px-6 py-4">{{ $p->maker?->name ?? '-' }}</td>
              <td class="px-6 py-4">{{ data_get($p->meta,'tipe','-') }}</td>

              <td class="px-6 py-4">
                <span class="inline-flex w-56 justify-center px-4 py-1.5 rounded-full text-xs border {{ $badge }}">
                  {{ $p->status?->label ?? '-' }}
                </span>
              </td>

              <td class="px-6 py-4">
                <x-pengajuan-actions :pengajuan="$p" role="ADMIN" />
              </td>
            </tr>
          @endforeach
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