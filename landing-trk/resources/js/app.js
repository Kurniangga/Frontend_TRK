import './bootstrap';

document.addEventListener('DOMContentLoaded', function () {
    const btns = document.getElementsByClassName('togglePassword');

    for (const btn of btns) {
        btn.addEventListener('click', function () {
            // Find the closest wrapper container or parent element of this button
            const wrapper = btn.closest('.form-group') || btn.parentElement;
            
            // Find the password input specifically inside this wrapper
            const passIn = wrapper.querySelector('.password');

            if (passIn) {
                const type = passIn.getAttribute('type') === 'password' ? 'text' : 'password';
                passIn.setAttribute('type', type);
            }
        });
    }
});