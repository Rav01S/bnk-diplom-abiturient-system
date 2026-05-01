/**
 * Portal App JS — sidebar toggle, image preview, utility functions.
 */
document.addEventListener('DOMContentLoaded', function () {
  'use strict';

  // Sidebar toggle
  const burger = document.getElementById('burgerBtn');
  const sidebar = document.getElementById('sidebar');
  const overlay = document.getElementById('sidebarOverlay');
  const closeSidebar = document.getElementById('closeSidebar');

  function toggleSidebar() {
    sidebar?.classList.toggle('-translate-x-full');
    overlay?.classList.toggle('hidden');
    document.body.classList.toggle('overflow-hidden');
  }

  burger?.addEventListener('click', toggleSidebar);
  overlay?.addEventListener('click', toggleSidebar);
  closeSidebar?.addEventListener('click', toggleSidebar);
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && sidebar && !sidebar.classList.contains('-translate-x-full')) {
      toggleSidebar();
    }
  });

  // Image preview on file inputs
  document.querySelectorAll('input[type="file"][accept*="image"]').forEach(function (input) {
    input.addEventListener('change', function (e) {
      const file = e.target.files?.[0];
      if (!file || !file.type.startsWith('image/')) return;

      const container = input.closest('div')?.querySelector('.photo-preview') ||
        input.parentElement?.nextElementSibling;
      if (!container) return;

      const reader = new FileReader();
      reader.onload = function (ev) {
        container.innerHTML = '<img src="' + ev.target.result + '" alt="Предпросмотр" class="max-h-48 rounded-lg shadow-sm object-cover">';
      };
      reader.readAsDataURL(file);
    });
  });

  // Auto-hide flash messages after 5 seconds
  document.querySelectorAll('[data-flash-message]').forEach(function (el) {
    setTimeout(function () {
      el.style.transition = 'opacity 0.5s';
      el.style.opacity = '0';
      setTimeout(function () { el.remove(); }, 500);
    }, 5000);
  });

  // Close modals on backdrop click
  document.querySelectorAll('[id$="Modal"]').forEach(function (modal) {
    modal.addEventListener('click', function (e) {
      if (e.target === modal) modal.classList.add('hidden');
    });
  });
});
