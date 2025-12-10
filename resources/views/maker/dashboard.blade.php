@extends('layouts.app') 
{{-- kalau belum punya file layout, bilang ya nanti aku buatin --}}

@section('content')
<div class="bg-gray-50 min-h-screen">

  <!-- Sidebar -->
  <aside class="fixed top-0 left-0 h-screen w-52 bg-indigo-600 z-50 flex flex-col">
    <div class="p-6 flex items-center gap-3 border-b border-white/20">
      <img src="{{ asset('images/logo.png') }}" alt="Logo" width="40" height="40" class="rounded-full">
      <span class="text-white text-xl font-semibold">Q-PRI</span>
    </div>
    
    <div class="flex-1 p-4">
      <div class="flex items-center justify-center mb-6">
        <img src="{{ asset('images/avatar.png') }}" alt="User avatar" width="60" height="60" class="rounded-full">
      </div>

      {{-- Contoh user name dinamis --}}
      <p class="text-white text-center font-medium mb-8">{{ auth()->user()->name ?? 'User' }}</p>
      
      <nav>
        <ul class="space-y-2">
          <li>
            <a href="{{ route('maker.dashboard') }}" class="flex items-center gap-3 px-4 py-3 text-white bg-white/20 rounded-lg">
              @svg('tabler-grid') 
              <span>Dashboard</span>
            </a>
          </li>

          <li>
            <a href="#" class="flex items-center gap-3 px-4 py-3 text-white hover:bg-white/10 rounded-lg transition">
              @svg('tabler-user') 
              <span>Management</span>
            </a>
          </li>

          <li>
            <a href="#" class="flex items-center gap-3 px-4 py-3 text-white hover:bg-white/10 rounded-lg transition">
              @svg('tabler-file') 
              <span>Pengajuan</span>
            </a>
          </li>

          <li>
            <a href="#" class="flex items-center gap-3 px-4 py-3 text-white hover:bg-white/10 rounded-lg transition">
              @svg('tabler-info-circle') 
              <span>Information</span>
            </a>
          </li>
        </ul>
      </nav>
    </div>
    
    <div class="p-4 border-t border-white/20">
      <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button class="flex items-center gap-3 px-4 py-3 text-white hover:bg-white/10 rounded-lg transition w-full">
          @svg('tabler-logout')
          <span>Log out</span>
        </button>
      </form>
    </div>
  </aside>

  <!-- Main Content -->
  <div class="ml-52">

    <!-- Navbar -->
    <header class="sticky top-0 bg-white border-b border-gray-200 z-40 px-8 py-4">
      <div class="flex items-center justify-between gap-4">
        
        <!-- Search -->
        <div class="flex-1 max-w-md mx-8">
          <div class="relative">
            <input type="search" placeholder="Search" 
              class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" width="18" height="18">
              <circle cx="11" cy="11" r="8"></circle>
              <path d="m21 21-4.35-4.35"></path>
            </svg>
          </div>
        </div>

        <!-- Type filter -->
        <div class="relative">
          <select class="pl-4 pr-10 py-2 border border-gray-300 rounded-lg bg-white focus:ring-indigo-500">
            <option>Type : All</option>
            <option>Type A</option>
            <option>Type B</option>
            <option>Type C</option>
          </select>
          <svg class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400" width="16" height="16">
            <polyline points="6 9 12 15 18 9"></polyline>
          </svg>
        </div>
      </div>
    </header>

    <main class="p-8">

      <!-- Summary Cards -->
      <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        
        {{-- CARD 1 --}}
        <div class="bg-white rounded-lg p-6 border hover:shadow-md">
          <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-lg bg-indigo-100 flex items-center justify-center">
              @svg('tabler-folder', 'text-indigo-600 w-6 h-6')
            </div>
            <div>
              <p class="text-3xl font-semibold text-indigo-600">{{ $total_project ?? 0 }}</p>
              <p class="text-sm text-gray-600">Total Project</p>
            </div>
          </div>
        </div>

        {{-- CARD 2 --}}
        <div class="bg-white rounded-lg p-6 border hover:shadow-md">
          <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-lg bg-green-100 flex items-center justify-center">
              @svg('tabler-check', 'text-green-600 w-6 h-6')
            </div>
            <div>
              <p class="text-3xl font-semibold text-green-600">{{ $approved ?? 0 }}</p>
              <p class="text-sm text-gray-600">Approved</p>
            </div>
          </div>
        </div>

        {{-- CARD 3 --}}
        <div class="bg-white rounded-lg p-6 border hover:shadow-md">
          <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-lg bg-yellow-100 flex items-center justify-center">
              @svg('tabler-clock', 'text-yellow-600 w-6 h-6')
            </div>
            <div>
              <p class="text-3xl font-semibold text-yellow-600">{{ $waiting ?? 0 }}</p>
              <p class="text-sm text-gray-600">Waiting For Approval</p>
            </div>
          </div>
        </div>

        {{-- CARD 4 --}}
        <div class="bg-white rounded-lg p-6 border hover:shadow-md">
          <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-lg bg-red-100 flex items-center justify-center">
              @svg('tabler-x', 'text-red-600 w-6 h-6')
            </div>
            <div>
              <p class="text-3xl font-semibold text-red-600">{{ $rejected ?? 0 }}</p>
              <p class="text-sm text-gray-600">Rejected</p>
            </div>
          </div>
        </div>

      </section>

      <!-- Approved Table -->
      <section class="bg-white rounded-lg border mb-8 overflow-hidden">
        <table class="w-full">
          <thead class="bg-indigo-600 text-white">
            <tr>
              <th class="px-6 py-4 text-left">ID</th>
              <th class="px-6 py-4 text-left">Name</th>
              <th class="px-6 py-4 text-left">Pemilik</th>
              <th class="px-6 py-4 text-left">Type</th>
              <th class="px-6 py-4 text-left">Created</th>
              <th class="px-6 py-4 text-left">Status</th>
            </tr>
          </thead>

          <tbody class="divide-y">
            @foreach ($approved_projects ?? [] as $p)
            <tr>
              <td class="px-6 py-4">{{ $p->id }}</td>
              <td class="px-6 py-4">{{ $p->name }}</td>
              <td class="px-6 py-4">{{ $p->owner }}</td>
              <td class="px-6 py-4">{{ $p->type }}</td>
              <td class="px-6 py-4">{{ $p->created_at }}</td>
              <td class="px-6 py-4">
                <span class="px-4 py-1.5 rounded-full text-xs border border-green-600 text-green-600 bg-green-50">
                  Approved
                </span>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </section>

      <!-- Rejected Table -->
      <section class="bg-white rounded-lg border mb-8 overflow-hidden">
        <table class="w-full">
          <thead class="bg-indigo-600 text-white">
            <tr>
              <th class="px-6 py-4 text-left">ID</th>
              <th class="px-6 py-4 text-left">Name</th>
              <th class="px-6 py-4 text-left">Pemilik</th>
              <th class="px-6 py-4 text-left">Type</th>
              <th class="px-6 py-4 text-left">Created</th>
              <th class="px-6 py-4 text-left">Status</th>
            </tr>
          </thead>

          <tbody class="divide-y">
            @foreach ($rejected_projects ?? [] as $p)
            <tr>
              <td class="px-6 py-4">{{ $p->id }}</td>
              <td class="px-6 py-4">{{ $p->name }}</td>
              <td class="px-6 py-4">{{ $p->owner }}</td>
              <td class="px-6 py-4">{{ $p->type }}</td>
              <td class="px-6 py-4">{{ $p->created_at }}</td>
              <td class="px-6 py-4">
                <span class="px-4 py-1.5 rounded-full text-xs border border-red-600 text-red-600 bg-red-50">
                  Rejected
                </span>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </section>

    </main>
  </div>
</div>
@endsection
