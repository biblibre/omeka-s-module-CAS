(function() {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        const userBarLogin = document.querySelector('#user-bar .login');
        if (userBarLogin) {
            const loginWithCas = document.createElement('a');
            loginWithCas.href = CAS.login_url;
            loginWithCas.innerText = Omeka.jsTranslate('Log in with CAS')
            loginWithCas.classList.add('login-with-cas');
            userBarLogin.insertAdjacentHTML('afterend', loginWithCas.outerHTML);
        }
    });
})();
