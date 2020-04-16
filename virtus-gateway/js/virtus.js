const v = jQuery.noConflict();
;(() => {
  v(document).ready(document => {
    v('#billing_income')
      .attr({
        min: 1500.00,
        max: 30000.00,
        required: true,
        maxlength: 10
      })
      .mask('#0,00', {reverse: true});

    v('#billing_cpf').mask('000.000.000-00');

    v('#billing_postcode').mask('00000-000', {
      onComplete: cep => {
        v.get(`https://viacep.com.br/ws/${cep}/json`, data => {
          if(!data.erro) {
            let { unidade, gia, ibge, ...viacep} = data;

            v('#billing_address_1').val(viacep.logradouro);
            v('#billing_address_2').val(viacep.complemento);
            v('#billing_neighborhood').val(viacep.bairro);
            v('#billing_city').val(viacep.localidade);
            v('#billing_state').val(viacep.uf);
          }
        });
      }
    });
  });
})()
