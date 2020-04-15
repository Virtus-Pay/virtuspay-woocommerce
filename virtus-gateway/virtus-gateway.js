const $ = jQuery.noConflict();
$(document).ready(() => {
  $('#billing_cpf').mask('000.000.000-00');
  $('#billing_birthdate').mask('00/00/0000');
  $('#billing_phone').mask('(00) 0-0000-0000');
  $('#billing_income')
    .attr({
      step: 0.1,
      min: 1000,
      max: 30000
    });

  $('#billing_postcode').mask('00000-000', {
    onComplete: cep => {
      $.get(`https://viacep.com.br/ws/${cep}/json`, viacep => {
        if(viacep.erro) return;

        let {logradouro, complemento, bairro, localidade, uf} = viacep;
        $('#billing_address_1').val(logradouro);
        $('#billing_address_2').val(complemento);
        $('#billing_neighborhood').val(bairro);
        $('#billing_city').val(localidade);
        $('#billing_state').val(uf);
      });
    }
  });

  $('.mobilePhone').mask('00 0-0000-0000');
});
