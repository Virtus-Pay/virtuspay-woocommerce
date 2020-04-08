const v = jQuery.noConflict();
v(document).ready(() => {
    v('.cpf').mask('000.000.000-00', {
        onComplete: document => {
            v('.cpf').css('border-color', 'green');
        }
    });
});
