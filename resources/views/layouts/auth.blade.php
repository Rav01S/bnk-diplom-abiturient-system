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
            primary: { DEFAULT: 'var(--auth-primary)', 50: 'var(--auth-primary-50)', 100: 'var(--auth-primary-100)', 600: 'var(--auth-primary)', 700: 'var(--auth-primary-700)' },
            secondary: { DEFAULT: '#48b59b', 50: '#effaf7', 100: '#d8f2eb', 600: '#48b59b', 700: '#2f8f79' },
            accent: { orange: '#f47a00', blue: '#0e5d9f', steel: '#4d759d', yellow: '#ffbf00', red: '#ed3216', magenta: '#b33c96' },
            success: { DEFAULT: '#48b59b', 600: '#2f8f79' },
            warning: { DEFAULT: '#f47a00', 600: '#d86600' },
            danger: { DEFAULT: '#ed3216', 600: '#c92510' },
            gray: { 50: '#f8f8f7', 100: '#eeeeec', 200: '#dcdad7', 300: '#c4c0bc', 500: '#736f6b', 700: '#3f3a38', 900: '#2b2828' }
          },
          borderRadius: { DEFAULT: '0.5rem', lg: '0.5rem' },
          boxShadow: { sm: '0 1px 2px 0 rgb(0 0 0 / 0.05)' }
        }
      }
    }
  </script>
  <style>
    :root {
      --auth-primary: #252581;
      --auth-primary-50: #f2f3ff;
      --auth-primary-100: #e2e5ff;
      --auth-primary-700: #1b1b68;
    }

    body {
      background:
        linear-gradient(135deg, var(--auth-primary), rgba(14, 93, 159, 0.82)),
        radial-gradient(circle at 16% 18%, rgba(72, 181, 155, 0.38), transparent 30%),
        radial-gradient(circle at 82% 12%, rgba(255, 191, 0, 0.3), transparent 24%),
        radial-gradient(circle at 72% 84%, rgba(179, 60, 150, 0.34), transparent 28%),
        var(--auth-primary);
    }
    .focus-ring:focus { outline: 2px solid #48b59b; outline-offset: 2px; }
    [aria-required="true"]::after { content: " *"; color: #ed3216; }
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
    .auth-color-button {
      border-color: rgba(255, 255, 255, 0.55);
      background: var(--swatch);
    }
    .auth-color-button[aria-pressed="true"] {
      box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.95), 0 0 0 5px rgba(0, 0, 0, 0.22);
    }
  </style>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4 pb-20">
  @yield('content')
  <div class="fixed inset-x-0 bottom-3 z-20 flex justify-center px-4">
    <div class="flex items-center gap-2 rounded-lg border border-white/30 bg-white/15 px-3 py-2 shadow-sm backdrop-blur" aria-label="Цвет оформления">
      @foreach([
        ['name' => 'Синий', 'color' => '#252581', 'soft' => '#e2e5ff', 'dark' => '#1b1b68'],
        ['name' => 'Зелёный', 'color' => '#48b59b', 'soft' => '#d8f2eb', 'dark' => '#2f8f79'],
        ['name' => 'Оранжевый', 'color' => '#f47a00', 'soft' => '#fff0df', 'dark' => '#d86600'],
        ['name' => 'Стальной', 'color' => '#4d759d', 'soft' => '#e7eef5', 'dark' => '#35577a'],
        ['name' => 'Жёлтый', 'color' => '#ffbf00', 'soft' => '#fff7d6', 'dark' => '#c28f00'],
        ['name' => 'Красный', 'color' => '#ed3216', 'soft' => '#ffe4df', 'dark' => '#c92510'],
        ['name' => 'Голубой', 'color' => '#0e5d9f', 'soft' => '#e0effb', 'dark' => '#084577'],
        ['name' => 'Маджента', 'color' => '#b33c96', 'soft' => '#f7e3f1', 'dark' => '#84266d'],
        ['name' => 'Графит', 'color' => '#2b2828', 'soft' => '#eeeeec', 'dark' => '#171515'],
      ] as $theme)
        <button
          type="button"
          class="auth-color-button h-7 w-7 rounded-full border transition hover:scale-105 focus:outline-none focus:ring-2 focus:ring-white"
          style="--swatch: {{ $theme['color'] }}"
          data-auth-color="{{ $theme['color'] }}"
          data-auth-soft="{{ $theme['soft'] }}"
          data-auth-dark="{{ $theme['dark'] }}"
          title="{{ $theme['name'] }}"
          aria-label="{{ $theme['name'] }}"
          aria-pressed="false"
        ></button>
      @endforeach
    </div>
  </div>
  <script>
    const authThemeButtons = document.querySelectorAll('[data-auth-color]');
    let savedAuthTheme = null;

    try {
      savedAuthTheme = JSON.parse(localStorage.getItem('authTheme') || 'null');
    } catch (error) {
      localStorage.removeItem('authTheme');
    }

    function setAuthTheme(theme) {
      if (!theme) return;

      document.documentElement.style.setProperty('--auth-primary', theme.color);
      document.documentElement.style.setProperty('--auth-primary-50', theme.soft);
      document.documentElement.style.setProperty('--auth-primary-100', theme.soft);
      document.documentElement.style.setProperty('--auth-primary-700', theme.dark);

      authThemeButtons.forEach(button => {
        button.setAttribute('aria-pressed', button.dataset.authColor === theme.color ? 'true' : 'false');
      });
    }

    if (savedAuthTheme) {
      setAuthTheme(savedAuthTheme);
    } else {
      authThemeButtons[0]?.setAttribute('aria-pressed', 'true');
    }

    authThemeButtons.forEach(button => {
      button.addEventListener('click', () => {
        const theme = {
          color: button.dataset.authColor,
          soft: button.dataset.authSoft,
          dark: button.dataset.authDark,
        };

        localStorage.setItem('authTheme', JSON.stringify(theme));
        setAuthTheme(theme);
      });
    });
  </script>
</body>
</html>
