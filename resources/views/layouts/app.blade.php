<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ config('app.name', 'Q-PRI') }}</title>

  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

@php
  $roleId = (int)(auth()->user()->role_id ?? 0);

  // halaman yang hide sidebar
  $hideSidebar = request()->routeIs(
    'pengajuans.create',
    'pengajuans.edit',
    'pengajuans.show',
    'pengajuans.iag.edit'
  );

  $activeClass = fn($active) => $active
    ? 'flex items-center gap-3 px-4 py-3 rounded-lg bg-white/15 border border-white/25'
    : 'flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10';
@endphp

<body class="bg-gray-100 text-gray-900">

@if(!$hideSidebar)
  <div class="min-h-screen flex">

    {{-- ✅ SIDEBAR --}}
    <aside class="w-72 bg-indigo-600 text-white shrink-0 h-screen flex flex-col sticky top-0 z-40">

      <div class="px-6 py-5 border-b border-white/20 flex items-center gap-3">
        <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center font-bold">
          Q
        </div>
        <div class="leading-tight">
          <div class="font-semibold text-lg">Q-PRI</div>
          <div class="text-sm text-white/80">{{ auth()->user()->name ?? '-' }}</div>
        </div>
      </div>

      <nav class="flex-1 px-4 py-4 space-y-2 overflow-y-auto">

        <a href="{{ route('dashboard') }}" class="{{ $activeClass(request()->routeIs('dashboard')) }}">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" class="opacity-95">
            <path d="M4 13h8V4H4v9Zm0 7h8v-5H4v5Zm10 0h6V11h-6v9Zm0-18v7h6V2h-6Z" fill="currentColor"/>
          </svg>
          <span class="font-medium">Dashboard</span>
        </a>

        <a href="{{ route('pengajuans.index') }}" class="{{ $activeClass(request()->routeIs('pengajuans.*')) }}">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" class="opacity-95">
            <path d="M4 5h16v4H4V5Zm0 6h16v8H4v-8Z" stroke="currentColor" stroke-width="2"/>
          </svg>
          <span class="font-medium">Management</span>
        </a>

        @if($roleId === 1)
          <a href="{{ route('pengajuans.create') }}" class="{{ $activeClass(request()->routeIs('pengajuans.create')) }}">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" class="opacity-95">
              <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
            <span class="font-medium">Pengajuan</span>
          </a>
        @endif

        <a href="{{ route('information') }}" class="{{ $activeClass(request()->routeIs('information')) }}">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" class="opacity-95">
            <path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10Z"
                  stroke="currentColor" stroke-width="2"/>
            <path d="M12 10v7" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            <path d="M12 7h.01" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
          </svg>
          <span class="font-medium">Information</span>
        </a>

      </nav>

      <div class="px-4 py-4 border-t border-white/20">
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" class="opacity-95">
              <path d="M10 17l5-5-5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M15 12H3" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
              <path d="M21 3v18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
            <span class="font-medium">Log out</span>
          </button>
        </form>
      </div>

    </aside>

    {{-- ✅ CONTENT --}}
    <main class="flex-1 min-w-0 relative z-0">
      <div class="px-8 py-6 pb-28">
        @yield('content')
      </div>
    </main>

  </div>
@else
  <main class="min-h-screen">
    <div class="px-8 py-6 pb-28">
      @yield('content')
    </div>
  </main>
@endif

@stack('scripts')

{{-- ✅ FIXED BUTTON DITARUH DI LUAR SEMUA CONTAINER (PENTING!) --}}
@stack('fixed')

</body>
</html>