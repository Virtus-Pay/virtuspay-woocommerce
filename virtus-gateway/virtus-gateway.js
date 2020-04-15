const v = jQuery.noConflict();
v(document).ready(() => {
    v('.cpf').mask('000.000.000-00');

    v('.cep').mask('00000-000', {
      onComplete: cep => {
        v.get(`https://viacep.com.br/ws/${cep}/json`, viacep => {
          console.log(viacep)
        });
      }
    });

    v('.mobilePhone').mask('00 0-0000-0000');
});
