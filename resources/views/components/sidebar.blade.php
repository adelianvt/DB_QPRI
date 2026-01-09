@php
  $user = auth()->user();
  $roleId = (int) ($user->role_id ?? 0);

  $roleName = match($roleId) {
    1 => 'CRV',
    2 => 'GH CRV',
    3 => 'IAG',
    14 => 'GH IAG',
    default => 'User',
  };

  $is = fn($name) => request()->routeIs($name);

  $itemClass = function($active){
    return $active
      ? 'bg-white/20 text-white'
      : 'text-white/90 hover:bg-white/10';
  };
@endphp

{{-- ✅ JANGAN fixed + JANGAN nutup layar --}}
<aside class="w-64 bg-gradient-to-b from-indigo-700 to-indigo-600 z-10 flex flex-col">

  <div class="px-6 py-6 flex items-center gap-3 border-b border-white/20">
    <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center text-white font-semibold">
      Q
    </div>
    <div class="text-white text-xl font-semibold">Q-PRI</div>
  </div>

  <div class="px-6 py-6 border-b border-white/20">
    <div class="flex items-center justify-center mb-4">
      <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center text-white text-lg font-semibold">
        {{ strtoupper(substr($roleName,0,1)) }}
      </div>
    </div>

    <div class="text-center text-white font-semibold">{{ $roleName }}</div>
    <div class="text-center text-white/80 text-sm mt-1">{{ $user->name ?? 'User' }}</div>
  </div>

  <nav class="flex-1 px-4 py-6 space-y-2">
    <a href="{{ route('dashboard') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ $itemClass($is('dashboard')) }}">
      <span class="text-lg">▦</span>
      <span>Dashboard</span>
    </a>

    <a href="{{ route('pengajuans.index') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ $itemClass($is('pengajuans.*')) }}">
      <span class="text-lg">👤</span>
      <span>Management</span>
    </a>

    @if($roleId === 1)
      <a href="{{ route('pengajuans.create') }}"
         class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ $itemClass($is('pengajuans.create')) }}">
        <span class="text-lg">＋</span>
        <span>Pengajuan</span>
      </a>
    @endif

    <a href="{{ route('information') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ $itemClass($is('information')) }}">
      <span class="text-lg">ⓘ</span>
      <span>Information</span>
    </a>
  </nav>

  <div class="px-4 py-4 border-t border-white/20">
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit"
        class="w-full flex items-center gap-3 px-4 py-3 rounded-lg text-white/90 hover:bg-white/10 transition">
        <span class="text-lg">⟵</span>
        <span>Log out</span>
      </button>
    </form>
  </div>
</aside>