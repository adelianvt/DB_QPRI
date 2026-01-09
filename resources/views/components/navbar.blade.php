@php
  $route = \Illuminate\Support\Facades\Route::currentRouteName();
  $showSearch = in_array($route, ['dashboard', 'pengajuans.index'], true);

  $q = request('q', '');
  $type = request('type', 'all');
@endphp

{{-- STICKY biar nempel di atas --}}
<div class="sticky top-0 z-40 bg-white border-b">
  <div class="px-4 sm:px-6 py-3">
    <div class="flex flex-col sm:flex-row sm:items-center gap-3">

      @if($showSearch)
        <form method="GET" action="{{ url()->current() }}" class="w-full flex flex-col sm:flex-row sm:items-center gap-3">

          {{-- Search --}}
          <div class="w-full sm:flex-1 sm:max-w-2xl">
            <div class="relative">
              <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">🔍</span>
              <input
                type="text"
                name="q"
                value="{{ $q }}"
                placeholder="Search judul / pemilik"
                class="w-full pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
              >
            </div>
          </div>

          {{-- Filter --}}
          <div class="w-full sm:w-56">
            <select
              name="type"
              onchange="this.form.submit()"
              class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
              <option value="all" {{ $type==='all' ? 'selected' : '' }}>Type : All</option>
              <option value="internal" {{ $type==='internal' ? 'selected' : '' }}>Pengembangan Internal</option>
              <option value="pengadaan" {{ $type==='pengadaan' ? 'selected' : '' }}>Pengadaan</option>
            </select>
          </div>

        </form>
      @endif

    </div>
  </div>
</div>
