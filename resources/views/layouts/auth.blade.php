<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Приёмная комиссия ГАПОУ Бугурусланский нефтяной колледж')</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <style>
    :root {
      --auth-primary: #0ea5e9;
      --auth-primary-50: #eff6ff;
      --auth-primary-100: #dbeafe;
      --auth-primary-700: #0284c7;
      --theme-secondary: #0ea5e9;
      --theme-secondary-50: #eff6ff;
      --theme-secondary-100: #dbeafe;
      --theme-secondary-700: #0284c7;
      --theme-success: #0ea5e9;
      --theme-success-600: #0284c7;
      --theme-warning: #38bdf8;
      --theme-warning-600: #0ea5e9;
      --theme-danger: #0284c7;
      --theme-danger-600: #0369a1;
    }

    body {
      background: linear-gradient(135deg, #2a2588 0%, #243b93 42%, #0f6bb7 72%, #0ea5e9 100%);
      background-attachment: fixed;
    }
    .focus-ring:focus { outline: 2px solid #93c5fd; outline-offset: 2px; }
    [aria-required="true"]::after { content: " *"; color: #0ea5e9; }
    .professionalitet-mark {
      display: grid;
      grid-template-columns: repeat(9, minmax(0, 1fr));
      gap: 0.375rem;
    }
    .professionalitet-mark span {
      display: block;
      height: 0.5rem;
      border-radius: 9999px;
    }
    .auth-card input:not([type="checkbox"]) {
      background-color: #ffffff;
    }
    .auth-card input:-webkit-autofill,
    .auth-card input:-webkit-autofill:hover,
    .auth-card input:-webkit-autofill:focus {
      -webkit-text-fill-color: #16233f;
      box-shadow: 0 0 0 1000px #ffffff inset;
      transition: background-color 9999s ease-in-out 0s;
    }
    .auth-chevron-button {
      position: relative;
      overflow: hidden;
    }
    .auth-chevron-track {
      position: absolute;
      inset-block: 0;
      right: 1rem;
      display: flex;
      align-items: center;
      gap: 0.2rem;
      opacity: 0.55;
      pointer-events: none;
    }
    .auth-chevron {
      width: 1rem;
      height: 1rem;
      flex: 0 0 auto;
      border-top: 0.32rem solid #0284c7;
      border-right: 0.32rem solid #0284c7;
      transform: rotate(45deg) skew(8deg, 8deg);
    }
  </style>
</head>
<body class="bg-primary-600 min-h-screen flex items-center justify-center p-4">
  @yield('content')
  @yield('scripts')
</body>
</html>
