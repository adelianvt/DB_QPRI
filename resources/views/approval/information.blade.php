<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Information | Approver - Q-PRI</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap');
    body { font-family: 'Inter', sans-serif; }
  </style>
</head>
<body class="bg-gray-50">

@include('components.sidebar')

<div class="ml-52 min-h-screen">
  @include('components.navbar')

  <main class="p-8 pt-6">
    <h1 class="text-xl font-semibold text-gray-900 mb-4">Information</h1>

    <div class="bg-white border rounded-xl p-6 text-gray-700">
      <p class="font-medium mb-2">Panduan Approver</p>
      <ul class="list-disc pl-5 space-y-1 text-sm">
        <li>Masuk menu <b>Management</b> untuk lihat daftar approvals.</li>
        <li>Gunakan action (View) untuk cek detail pengajuan.</li>
        <li>Approval dilakukan sesuai flow status yang sudah ditentukan.</li>
      </ul>
    </div>
  </main>
</div>

</body>
</html>
