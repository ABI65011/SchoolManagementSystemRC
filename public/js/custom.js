document.addEventListener("DOMContentLoaded", () => {
    const switcher = document.getElementById("themeSwitcher");
    const prefersDark = window.matchMedia("(prefers-color-scheme: dark)");

    function applyTheme(isDark, persist = true) {
        document.body.setAttribute("data-bs-theme", isDark ? "dark" : "light");
        if (persist) {
            localStorage.setItem("theme", isDark ? "dark" : "light");
        }
        if (switcher) {
            switcher.checked = isDark;
        }
    }

    function getInitialTheme() {
        const saved = localStorage.getItem("theme");
        if (saved) {
            return saved === "dark";
        }
        return prefersDark.matches; // fallback to system
    }

    // Apply initial theme
    applyTheme(getInitialTheme(), false);

    // User toggles the switch
    switcher?.addEventListener("change", e => {
        applyTheme(e.target.checked, true);
    });

    // If system theme changes, adopt it only if user hasn’t chosen manually
    prefersDark.addEventListener("change", e => {
        if (!localStorage.getItem("theme")) {
            applyTheme(e.matches, false);
        }
    });
});
