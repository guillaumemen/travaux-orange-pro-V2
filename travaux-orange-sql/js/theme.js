const THEME_KEY = "travauxTheme";

function applyTheme(theme) {
  document.documentElement.dataset.theme = theme;
  localStorage.setItem(THEME_KEY, theme);
}

function getPreferredTheme() {
  const stored = localStorage.getItem(THEME_KEY);
  if (stored === "light" || stored === "dark") {
    return stored;
  }

  // Respecte la préférence système si dispo (prefers-color-scheme)[web:70]
  const prefersDark =
    window.matchMedia &&
    window.matchMedia("(prefers-color-scheme: dark)").matches;

  return prefersDark ? "dark" : "light";
}

function updateToggleUI(theme) {
  const toggles = document.querySelectorAll("[data-theme-toggle]");
  toggles.forEach((btn) => {
    const icon = btn.querySelector("#themeIcon");
    const label = btn.querySelector("#themeLabel");
    if (!icon || !label) return;

    if (theme === "dark") {
      icon.textContent = "☀️";
      label.textContent = "Mode clair";
    } else {
      icon.textContent = "🌙";
      label.textContent = "Mode sombre";
    }
  });
}

function initTheme() {
  const theme = getPreferredTheme(); // stocké dans localStorage ou dérivé de la préférence système[web:68][web:71]
  document.documentElement.dataset.theme = theme;
  updateToggleUI(theme);

  const toggles = document.querySelectorAll("[data-theme-toggle]");
  toggles.forEach((btn) => {
    btn.addEventListener("click", () => {
      const current =
        document.documentElement.dataset.theme === "dark" ? "dark" : "light";
      const next = current === "dark" ? "light" : "dark";
      applyTheme(next);
      updateToggleUI(next);
    });
  });
}

document.addEventListener("DOMContentLoaded", initTheme);