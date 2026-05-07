<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Приёмная комиссия ГАПОУ Бугурусланский нефтяной колледж')</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <style>
    body { background: linear-gradient(180deg, #eff6ff 0%, #f8fbff 240px); }
    .focus-ring:focus { outline: 2px solid #0ea5e9; outline-offset: 2px; }
    [aria-required="true"]::after { content: " *"; color: #0284c7; }
    .sidebar-link.active { background-color: #0ea5e9; color: white; }
    .sidebar-link.active svg { color: white; }
    .sidebar-link:hover { color: #0284c7; }
    @yield('styles')
  </style>
</head>
<body class="bg-gray-50 min-h-screen">
  <div id="sidebarOverlay" class="fixed inset-0 bg-black/50 z-40 hidden lg:hidden" aria-hidden="true"></div>
  <aside id="sidebar" class="fixed top-0 left-0 z-50 h-full w-64 bg-white border-r border-gray-200 transform -translate-x-full lg:translate-x-0 transition-transform duration-200 ease-in-out">
    <div class="flex h-16 items-center justify-between border-b border-gray-200 bg-white px-3 py-2">
      <div class="flex min-w-0 items-center gap-3">
        <div class="flex h-9 w-9 shrink-0 items-center justify-center overflow-hidden rounded-lg bg-white shadow-sm ring-2 ring-primary-100">
          <img src="{{ asset('logo.gif') }}" alt="Логотип БНК" class="h-8 w-8 object-contain">
        </div>
        <div class="min-w-0">
          @if(auth()->user()->isAdmin())
            <p class="truncate text-xs font-semibold uppercase tracking-wide text-primary-700">Панель администратора</p>
            <p class="mt-0.5 truncate text-xs text-gray-500">Управление порталом</p>
          @else
            <p class="truncate text-xs font-semibold uppercase tracking-wide text-primary-700">Приёмная комиссия</p>
            <p class="mt-0.5 leading-tight text-[13px] font-medium text-gray-900">ГАПОУ БНК</p>
          @endif
        </div>
      </div>
      <button id="closeSidebar" class="lg:hidden p-1 text-gray-500 hover:text-gray-700" aria-label="Закрыть меню">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>
    <nav class="p-4 space-y-1" aria-label="Основная навигация">
      @yield('sidebar')
    </nav>
    <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-200">
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="flex items-center w-full px-3 py-2 text-sm font-medium text-danger hover:bg-blue-50 rounded-lg transition">
          <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
          Выйти
        </button>
      </form>
    </div>
  </aside>

  <div class="lg:ml-64">
    <header class="fixed top-0 right-0 left-0 z-30 flex h-16 items-center justify-between border-b border-gray-200 bg-white px-4 lg:left-64">
      <div class="flex min-w-0 items-center space-x-3">
        <button id="burgerBtn" class="lg:hidden p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg" aria-label="Открыть меню">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
        <h1 class="truncate text-lg font-semibold text-gray-900">@yield('header')</h1>
      </div>
      <div class="flex items-center space-x-2">
        @if(!auth()->user()->isApplicant())
        <a href="{{ route('password.edit') }}" class="p-2 text-gray-500 hover:text-primary-600 hover:bg-gray-100 rounded-lg transition" title="Сменить пароль">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
        </a>
        @endif
        <div class="flex items-center space-x-2 ml-2 pl-2 border-l border-gray-200">
          <div class="w-8 h-8 rounded-full bg-primary-100 flex items-center justify-center">
            <span class="text-sm font-medium text-primary-700">{{ auth()->user()->initials }}</span>
          </div>
          <span class="hidden sm:inline text-sm font-medium text-gray-700">{{ auth()->user()->display_name }}</span>
        </div>
      </div>
    </header>

    <main class="p-4 pt-20 sm:p-6 sm:pt-22">
      @if(session('success'))
      <div data-flash-message class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg text-sm text-blue-800 flex items-center">
        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('success') }}
      </div>
      @endif

      @if($errors->any())
      <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg text-sm text-blue-800">
        <ul class="list-disc list-inside">
          @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
      @endif

      @yield('content')
    </main>
  </div>

  <script src="{{ asset('js/app.js') }}"></script>
  @yield('scripts')
</body>
</html>
