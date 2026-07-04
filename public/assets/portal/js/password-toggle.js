(function () {
    document.addEventListener('click', function (event) {
        const button = event.target.closest('[data-password-toggle]');
        if (!button) {
            return;
        }

        const wrap = button.closest('.password-input-row, .password-toggle-wrap, .password-field-wrap');
        const input = wrap ? wrap.querySelector('input') : null;
        const icon = button.querySelector('i');
        if (!input || !icon) {
            return;
        }

        const isHidden = input.type === 'password';
        input.type = isHidden ? 'text' : 'password';
        icon.classList.toggle('bi-eye', !isHidden);
        icon.classList.toggle('bi-eye-slash', isHidden);
        button.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
    });
})();
