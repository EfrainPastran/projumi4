
document.addEventListener("DOMContentLoaded", function () {
    const toggleBtn = document.getElementById('toggle-menu');
    const menu = document.getElementById('menu-navegacion');

    toggleBtn.addEventListener('click', function () {
        menu.classList.toggle('open');
    });
