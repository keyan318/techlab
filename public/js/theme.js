/*
 * TechLab theme (dark / light).
 * Loaded synchronously in <head> so the saved theme is applied before first paint (no flash).
 * Persisted in localStorage under "techlab_theme"; first visit follows the OS preference.
 */
(function () {
  var KEY = 'techlab_theme';
  var root = document.documentElement;

  function read() {
    try {
      var saved = localStorage.getItem(KEY);
      if (saved === 'light' || saved === 'dark') return saved;
    } catch (e) {}
    return window.matchMedia && window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark';
  }

  function apply(theme) {
    root.setAttribute('data-theme', theme);
    root.classList.toggle('dark', theme === 'dark');
    root.style.colorScheme = theme;
    var meta = document.querySelector('meta[name="theme-color"]');
    if (meta) meta.setAttribute('content', theme === 'light' ? '#f4f6ff' : '#06061a');
    window.dispatchEvent(new CustomEvent('techlab:theme', { detail: theme }));
  }

  window.techlabTheme = {
    get: function () { return root.getAttribute('data-theme') || 'dark'; },
    set: function (theme) {
      try { localStorage.setItem(KEY, theme); } catch (e) {}
      apply(theme);
    },
    toggle: function () {
      root.classList.add('theme-anim');
      this.set(this.get() === 'light' ? 'dark' : 'light');
      clearTimeout(this._t);
      this._t = setTimeout(function () { root.classList.remove('theme-anim'); }, 350);
    },
  };

  apply(read());

  // keep multiple tabs in sync
  window.addEventListener('storage', function (e) {
    if (e.key === KEY && (e.newValue === 'light' || e.newValue === 'dark')) apply(e.newValue);
  });
})();
