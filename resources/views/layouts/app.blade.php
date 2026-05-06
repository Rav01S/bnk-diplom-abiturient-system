<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Приёмная комиссия ГАПОУ Бугурусланский нефтяной колледж')</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: { sans: ['Inter', 'sans-serif'] },
          colors: {
            primary: { DEFAULT: '#0b5f56', 50: '#edf7f4', 100: '#d6eee8', 600: '#0b5f56', 700: '#074c45' },
            secondary: { DEFAULT: '#c74b00', 50: '#fff4ec', 100: '#ffe5d2', 600: '#c74b00', 700: '#a53d00' },
            success: { DEFAULT: '#4d7c3f', 600: '#3f6833' },
            warning: { DEFAULT: '#c74b00', 600: '#a53d00' },
            danger: { DEFAULT: '#dc2626', 600: '#b91c1c' },
            gray: { 50: '#f7f8f5', 100: '#ecefeb', 200: '#d8ded8', 300: '#bbc6bf', 500: '#687870', 700: '#31443e', 900: '#162622' }
          },
          borderRadius: { DEFAULT: '0.5rem', lg: '0.5rem' },
          boxShadow: { sm: '0 1px 2px 0 rgb(0 0 0 / 0.05)' }
        }
      }
    }
  </script>
  <style>
    body { background: linear-gradient(180deg, #edf7f4 0%, #f7f8f5 240px); }
    .focus-ring:focus { outline: 2px solid #0b5f56; outline-offset: 2px; }
    [aria-required="true"]::after { content: " *"; color: #dc2626; }
    .sidebar-link.active { background-color: #0b5f56; color: white; }
    .sidebar-link.active svg { color: white; }
    .sidebar-link:hover { color: #074c45; }
    @yield('styles')
  </style>
</head>
<body class="bg-gray-50 min-h-screen">
  <div id="sidebarOverlay" class="fixed inset-0 bg-black/50 z-40 hidden lg:hidden" aria-hidden="true"></div>
  <aside id="sidebar" class="fixed top-0 left-0 z-50 h-full w-64 bg-white border-r border-gray-200 transform -translate-x-full lg:translate-x-0 transition-transform duration-200 ease-in-out">
    <div class="flex items-center justify-between h-16 px-4 border-b border-gray-200">
      <div class="flex min-w-0 items-center space-x-3">
        <div class="w-8 h-8 rounded-lg bg-primary-600 flex items-center justify-center">
          @if(auth()->user()->isAdmin())
          <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
          @else
          <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
          @endif
        </div>
        <span class="font-semibold text-gray-900">
          @if(auth()->user()->isAdmin()) Панель администратора @else Приёмная комиссия ГАПОУ Бугурусланский нефтяной колледж @endif
        </span>
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
        <button type="submit" class="flex items-center w-full px-3 py-2 text-sm font-medium text-danger hover:bg-red-50 rounded-lg transition">
          <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
          Выйти
        </button>
      </form>
    </div>
  </aside>

  <div class="lg:ml-64">
    <header class="sticky top-0 z-30 bg-white border-b border-gray-200 px-4 py-3 flex items-center justify-between">
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

    <main class="p-4 sm:p-6">
      @if(session('success'))
      <div data-flash-message class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-800 flex items-center">
        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('success') }}
      </div>
      @endif

      @if($errors->any())
      <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-800">
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
