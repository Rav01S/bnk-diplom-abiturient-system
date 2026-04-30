<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Портал приёмной комиссии')</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: { sans: ['Inter', 'sans-serif'] },
          colors: {
            primary: { DEFAULT: '#1e3a8a', 50: '#eff6ff', 100: '#dbeafe', 600: '#1e3a8a', 700: '#1d4ed8' },
            success: { DEFAULT: '#059669', 600: '#047857' },
            warning: { DEFAULT: '#d97706', 600: '#b45309' },
            danger: { DEFAULT: '#dc2626', 600: '#b91c1c' },
            gray: { 50: '#f8fafc', 100: '#f1f5f9', 200: '#e2e8f0', 300: '#cbd5e1', 500: '#64748b', 700: '#334155', 900: '#0f172a' }
          },
          borderRadius: { DEFAULT: '0.5rem', lg: '0.5rem' },
          boxShadow: { sm: '0 1px 2px 0 rgb(0 0 0 / 0.05)' }
        }
      }
    }
  </script>
  <style>
    .focus-ring:focus { outline: 2px solid #1e3a8a; outline-offset: 2px; }
    [aria-required="true"]::after { content: " *"; color: #dc2626; }
  </style>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4">
  @yield('content')
</body>
</html>
