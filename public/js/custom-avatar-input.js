let avatarImg = document.getElementById('avatar-img');
let avatarInput = document.getElementById('avatar-input');
let avatarLabel = document.getElementById('avatar-label');

avatarImg.addEventListener('click', function() {
    avatarInput.click();
});

avatarInput.addEventListener('change', function() {
    const file = this.files[0];

    if (file) {
        const reader = new FileReader();

        reader.addEventListener('load', function() {
            avatarImg.src = this.result;
        });

        reader.readAsDataURL(file);
        avatarLabel.textContent = file.name;
    }
});