let avatarImg2 = document.getElementById('acc-avatar-img');
let avatarInput2 = document.getElementById('acc-avatar-input');
let avatarLabel2 = document.getElementById('acc-avatar-label');

avatarImg2.addEventListener('click', function() {
    avatarInput2.click();
});

avatarInput2.addEventListener('change', function() {
    const file2 = this.files[0];

    if (file2) {
        const reader2 = new FileReader();

        reader2.addEventListener('load', function() {
            avatarImg2.src = this.result;
        });

        reader2.readAsDataURL(file2);
        avatarLabel2.textContent = file2.name;
    }
});