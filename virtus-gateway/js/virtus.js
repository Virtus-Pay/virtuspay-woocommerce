const v = jQuery.noConflict();
;(() => {
  const getInstallments = () => {
    let data = {
          total_amount: v('#virtusInstallmentsList').data('amount'),
          cpf: v('#billing_cpf').val()
        }

    v.post(`/wp-json/virtuspay/v1.0/installments`, data, response => {
      let {installments, details} = response;
      if(installments.length) {
        v('#virtusInstallmentsList > tbody > tr:first-child').toggle();

        for(let item of installments) {
          v('#virtusInstallmentsList > tbody').append(`
            <tr>
              <td>${item.parcelas}</td>
              <td>R$ ${item.entrada}</td>
              <td>R$ ${item.restante}</td>
              <td>R$ ${item.total}</td>
            </tr>
            `);
        }
      }
    });
  };

  const radioVirtusPaymentCheckout = '#payment_method_virtuspay';
  v(document).on('change', radioVirtusPaymentCheckout, () => {
    if(v(radioVirtusPaymentCheckout).is(':checked')) getInstallments();
  });

  v(document).ready(() => {
    v('#billing_income')
      .attr({
        min: '1500,00',
        max: '30000,00',
        required: true,
        maxlength: 10
      })
      .mask('#0,00', {reverse: true});

    v('#billing_cpf').mask('000.000.000-00');
    v('#billing_birthdate')
      .attr({
        pattern: "[0-9]{2}\/[0-9]{2}\/[0-9]{4}",
        min: 10,
        max: 10
      })
      .mask('00/00/0000', {
        placeholder: '00/00/0000'
      });

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
