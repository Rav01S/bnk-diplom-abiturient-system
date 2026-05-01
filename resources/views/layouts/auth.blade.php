<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Приёмная комиссия Бугурусланского нефтяного колледжа')</title>
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
    body {
      background:
        linear-gradient(135deg, rgba(7, 76, 69, 0.92), rgba(11, 95, 86, 0.74)),
        radial-gradient(circle at 15% 15%, rgba(199, 75, 0, 0.18), transparent 32%),
        #edf7f4;
    }
    .focus-ring:focus { outline: 2px solid #0b5f56; outline-offset: 2px; }
    [aria-required="true"]::after { content: " *"; color: #dc2626; }
  </style>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4">
  @yield('content')
</body>
</html>
