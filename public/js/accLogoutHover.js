let btn = document.getElementById('accLogoutBtn');
let img = document.getElementById('accLogoutImg');

btn.addEventListener('mouseenter', function() {
    btn.style.background = '#000000';
    btn.style.border = '1px solid #000000';
    img.src = '/storage/images/exit2.svg';
});

btn.addEventListener('mouseleave', function() {
    btn.style.background = '#FFC107';
    btn.style.border = '1px solid #FFC107';
    img.src = '/storage/images/exit.svg';
});