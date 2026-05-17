document.addEventListener('DOMContentLoaded', function () {
  const storedTheme = localStorage.getItem('voyage-theme') || 'dark';
  setTheme(storedTheme);

  const toggleButton = document.getElementById('themeToggle');
  if (toggleButton) {
    toggleButton.addEventListener('click', function () {
      const activeTheme = document.body.classList.contains('dark-mode') ? 'light' : 'dark';
      setTheme(activeTheme);
    });
  }
});

function setTheme(theme) {
  document.body.classList.remove('light-mode', 'dark-mode');
  document.body.classList.add(`${theme}-mode`);
  localStorage.setItem('voyage-theme', theme);

  const status = document.getElementById('themeStatus');
  const button = document.getElementById('themeToggle');
  if (status) {
    status.textContent = theme === 'light' ? 'Light Mode' : 'Dark Mode';
  }
  if (button) {
    button.textContent = theme === 'light' ? 'Switch to Dark Mode' : 'Switch to Light Mode';
  }
}

// Profile dropdown handling
document.addEventListener('click', function (e) {
  // Toggle dropdown when clicking the profile button
  var btn = e.target.closest('.profile-menu > .headerProfileButton');
  if (btn) {
    var menu = btn.parentNode.querySelector('.profile-dropdown');
    var expanded = btn.getAttribute('aria-expanded') === 'true';
    // close any other open menus
    document.querySelectorAll('.profile-menu .headerProfileButton[aria-expanded="true"]').forEach(function(b) {
      if (b !== btn) {
        b.setAttribute('aria-expanded', 'false');
        var m = b.parentNode.querySelector('.profile-dropdown');
        if (m) m.style.display = 'none';
      }
    });
    if (menu) {
      btn.setAttribute('aria-expanded', expanded ? 'false' : 'true');
      menu.style.display = expanded ? 'none' : 'block';
    }
    return;
  }
  // Close dropdowns when clicking outside
  if (!e.target.closest('.profile-menu')) {
    document.querySelectorAll('.profile-menu .headerProfileButton[aria-expanded="true"]').forEach(function(b) {
      b.setAttribute('aria-expanded', 'false');
      var m = b.parentNode.querySelector('.profile-dropdown');
      if (m) m.style.display = 'none';
    });
  }
});
