const v = jQuery.noConflict();
const setupVPScripts = ()=> (() => {
  const getInstallments = () => {
    v('#billing_installment').html(`<option selected disabled>Carregando...</option>`);

    let data = {
      total_amount: v('#billing_installment').data('amount'),
      cpf: v('#billing_cpf').val()
    };
    
    try {
      v.post(`/wp-json/virtuspay/installments`, data, response => {
        let {installments, ...details} = response,
            template;

        v('#interestAndCet').html(`Juros de ${details.interest} a.m. CET máximo de ${details.cet}`);

        if(installments.length) {
          v('#billing_installment > option:first-child').toggle();

          let counter = 0;
          for(let item of installments) {
            if(parseInt(item.parcelas) === 1) {
              template = `
                <option value="${item.parcelas}">
                  À vista: R$ ${item.total}
                </option>
                `;
            }
            else {
              template = `
                <option value="${item.parcelas}"${counter === 0 ? ' selected' : ''}>
                  ${item.parcelas}x
                  (Entrada: R$ ${item.entrada} + R$ ${parseInt(item.parcelas-1)}x R$ ${item.restante})
                  Total: R$ ${item.total}
                </option>
                `;
            }

            v('#billing_installment').append(template);
            counter++;
          }

          v('#billing_installment > option').get(0).remove();
          v('#billing_installment').prepend(`<option disabled>Selecione a quantidade de parcelas</option>`);
        }
      });      
    }
    catch(e) {
      console.log(e);
      v('#billing_installment').toggle();
    }
  };

  const radioVirtusPaymentCheckout = '#payment_method_virtuspay';
  v(document).on('change', radioVirtusPaymentCheckout, () => {
    if(v(radioVirtusPaymentCheckout).is(':checked')) getInstallments();
  });

  v(document).ready(() => {
    if(v(radioVirtusPaymentCheckout).is(':checked')) getInstallments();

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
})();

v(document).ready(()=>{
  const paymentAbled = v('#payment');
  const url = window.location.href.replace('//','/').split('/');
  if(paymentAbled.length >0 && url.length > 4) setupVPScripts();
  v(document.body).on('updated_checkout',()=>{
    setupVPScripts();
  });
});






