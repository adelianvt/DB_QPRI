@props([
  'total' => 0,
  'approved' => 0,
  'waiting' => 0,
  'rejected' => 0,
])

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
  <div class="bg-white border rounded-xl p-6">
    <p class="text-sm text-gray-500">Total Project</p>
    <p class="text-3xl font-semibold text-indigo-600 mt-1">{{ $total }}</p>
  </div>

  <div class="bg-white border rounded-xl p-6">
    <p class="text-sm text-gray-500">Approved</p>
    <p class="text-3xl font-semibold text-green-600 mt-1">{{ $approved }}</p>
  </div>

  <div class="bg-white border rounded-xl p-6">
    <p class="text-sm text-gray-500">Waiting</p>
    <p class="text-3xl font-semibold text-yellow-600 mt-1">{{ $waiting }}</p>
  </div>

  <div class="bg-white border rounded-xl p-6">
    <p class="text-sm text-gray-500">Rejected</p>
    <p class="text-3xl font-semibold text-red-600 mt-1">{{ $rejected }}</p>
  </div>
</div>