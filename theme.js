// Flash Learning - Theme Manager
// Runs immediately to prevent flash of wrong theme
(function () {
    var theme = localStorage.getItem('fl_theme') || 'light';
    document.documentElement.setAttribute('data-theme', theme);
})();

window.toggleTheme = function () {
    var current = document.documentElement.getAttribute('data-theme');
    var next = current === 'dark' ? 'light' : 'dark';
    document.documentElement.setAttribute('data-theme', next);
    localStorage.setItem('fl_theme', next);
};
