@props([
  'total' => 0,
  'approved' => 0,
  'waiting' => 0,
  'rejected' => 0,
])

<style>
  .hover-lift{transition:transform 200ms ease-in, background-color 200ms ease-in;}
  .hover-lift:hover{transform:translateY(-2px);}
</style>

<section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
  <div class="bg-white rounded-lg p-6 border border-gray-200 hover-lift">
    <div class="flex items-center gap-4">
      <div class="w-12 h-12 rounded-lg bg-indigo-100 flex items-center justify-center">📁</div>
      <div>
        <p class="text-3xl font-semibold text-indigo-600">{{ $total }}</p>
        <p class="text-sm text-gray-600">Total Project</p>
      </div>
    </div>
  </div>

  <div class="bg-white rounded-lg p-6 border border-gray-200 hover-lift">
    <div class="flex items-center gap-4">
      <div class="w-12 h-12 rounded-lg bg-green-100 flex items-center justify-center">✅</div>
      <div>
        <p class="text-3xl font-semibold text-green-600">{{ $approved }}</p>
        <p class="text-sm text-gray-600">Approved</p>
      </div>
    </div>
  </div>

  <div class="bg-white rounded-lg p-6 border border-gray-200 hover-lift">
    <div class="flex items-center gap-4">
      <div class="w-12 h-12 rounded-lg bg-yellow-100 flex items-center justify-center">⏳</div>
      <div>
        <p class="text-3xl font-semibold text-yellow-600">{{ $waiting }}</p>
        <p class="text-sm text-gray-600">Waiting For Approval</p>
      </div>
    </div>
  </div>

  <div class="bg-white rounded-lg p-6 border border-gray-200 hover-lift">
    <div class="flex items-center gap-4">
      <div class="w-12 h-12 rounded-lg bg-red-100 flex items-center justify-center">❌</div>
      <div>
        <p class="text-3xl font-semibold text-red-600">{{ $rejected }}</p>
        <p class="text-sm text-gray-600">Rejected</p>
      </div>
    </div>
  </div>
</section>