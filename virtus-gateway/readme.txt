=== VirtusPay Gateway ===
Contributors: paulosouzainfo
Tags: woocommerce, gateway, payments, installment billet
Requires at least: 5.1
Tested up to: 5.0.8
Stable tag: 1.0.3
Requires PHP: 7.2
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Pagamentos para o WooCommerce de boletos parcelados através da VirtusPay.

== Descrição ==

Aqui temos como objetivo definir os passos de inclusão, configuração e testes para negociações através da VirtusPay, integrando um novo meio de pagamento dentro da sua instalação do Wordpress com o plugin WooCommerce já disponível.

Com o contato comercial, serão disponibilizadas as credenciais de acesso para liberação da VirtusPay, o boleto parcelado online, no checkout da sua loja com o WooCommerce.

*Dependências*

  * Ter um certificado SSL atualizado e válido para a sua loja virtual;
  * Preferencialmente ter em seu ambiente versões do PHP 7.2+;
  * Ter o seu Wordpress atualizado em versões 5+;
  * Ter o plugin Brazilian Market on Woocommerce instalado e configurado.

*Funcionalidades*

  * Configuração de credenciais para integração da plataforma com a VirtusPay;
  * Configuração para habilitar/desabilitar o plugin de forma simples sem a necessidade de desativação pelo Wordpress;
  * Configuração para ativação/desativação de testes, mesmo em ambiente de produção da sua loja;
  * Seleção do meio de pagamento VirtusPay no momento do checkout;
  * Automação de status de propostas para pagamentos abertos, em processamento, recusados ou cancelados e concluídos.

Boas vendas!

== Instalação ==

*Download e Descompactação*

Faça o download o arquivo zip que foi disponibilizado com o envio das suas credenciais de acesso e descompacte o arquivo /wp-contents/plugins caso você não tenha iniciado o processo de instalação diretamente pelo diretório de plugins do Wordpress.

Após a descompactação do arquivo, uma nova pasta será criada com o nome virtus-gateway, permitindo assim a sua visualização no seu diretório de plugins do seu painel administrativo, conforme a imagem abaixo.

![Plugin Desativado](https://ipfs.io/ipfs/Qmf4aBtBxJEYmomY9rsEGyvRjPJJTvE6oJF5nnLWqYKfzb?filename=Captura%20de%20tela%20de%202020-04-22%2011-09-06.png)

*Ativação*

Ainda no seu diretório de plugins do Wordpress, clique no link “ativar” localizado logo abaixo do nome do plugin.

Após ativo, um novo link disponibilizará um acesso direto para as configurações do plugin dentro do ambiente Woocommerce, conforme a imagem abaixo.

![Plugin Ativado](https://ipfs.io/ipfs/QmQH8xZBHAcKN9nwru4JQUka9NmBb1YgjMdLrZgQtAht72?filename=Captura%20de%20tela%20de%202020-04-22%2011-24-40.png)

Caso você não queria seguir neste caminho, você poderá acessar a página de configuração do plugin no seu painel administrativo em WooCommerce > Configurações > Pagamentos.

A seguir, você poderá alterar a ordem dos meios de pagamento, habilitar o plugin diretamente ou gerenciar a sua ativação/desativação como um novo meio de pagamento diretamente nas páginas de configurações.

![Gateway Desativado como forma de pagamento](https://ipfs.io/ipfs/QmYSgySXmxacFNTvJ7NtYvQuiuFsHcuni8qAbsY9tMr3DS?filename=Captura%20de%20tela%20de%202020-04-22%2011-30-19.png)

Clicando no botão localizado na extrema direita com o nome “Gerenciar”, será liberado o acesso à  página de configuração do plugin.

== Configuração ==

Já na página de configuração, certifique-se de já possuir as informações enviadas junto com o arquivo zip que contém o plugin.

![Página de configuração](https://ipfs.io/ipfs/QmRkSTqDoA3ybiAQTQevxGncmXwEeD7XFWUponmbpmPhUa?filename=Captura%20de%20tela%20de%202020-04-22%2011-34-04.png)

Caso você tenha alguma dúvida sobre a identificação de cada campo, o ícone (?) localizado entre os nomes dos campos e os campos de configurações te auxiliará com as informações de cada item.

*Descrição dos campos*

_Ativação_
![Ativação](https://ipfs.io/ipfs/QmcnES4h2iBy4XsBf7ViaQgiZGURbfS2Wf8zoiC925agFX?filename=Captura%20de%20tela%20de%202020-04-22%2011-38-16.png)

A ativação ou desativação de pagamentos influenciará na tomada de decisão do seu comprador. Mantenha o plugin ativo.

_Modo de Testes_
![Modo de Testes](https://ipfs.io/ipfs/QmStbUsb1uz5jvFwXD4A6VjjTez159h7tPekEXDRp7naPC?filename=Captura%20de%20tela%20de%202020-04-22%2011-38-22.png)

A ativação ou desativação de pagamentos influenciará na tomada de decisão do seu comprador. Mantenha o plugin ativo.

_URL de Retorno_
![URL de Retorno](https://ipfs.io/ipfs/QmUmiw2mtk9Cn3vvNMXubJPDBG9bVsv7PtdhxUNFM1kKMS?filename=Captura%20de%20tela%20de%202020-04-22%2011-38-29.png)

Link para onde devemos redirecionar o usuário após a validação do seu pagamento.

_Credencial de Homologação_
![Credencial de Homologação](https://ipfs.io/ipfs/QmYJTrEK4kn9XAzPLXjJWRWdMy2Wjg9XDGDhem82ccch7r?filename=Captura%20de%20tela%20de%202020-04-22%2011-38-36.png)

Autenticação de acesso para a API de dados em ambiente de testes / homologação.

_Credencial de Produção_
![Credencial de Produção](https://ipfs.io/ipfs/QmWKoU47wekReKzi8v68HZxtW5qFyuiLvNGpEP8yMTRZRy?filename=Captura%20de%20tela%20de%202020-04-22%2011-38-43.png)

Autenticação de acesso para a API de dados em ambiente de produção / publicação.

== Screenshots ==

*Checkout*

![Checkout](https://ipfs.io/ipfs/QmS5jqxTrYhW6p9zPAaK8jMuHGPxv4zgSAcbs3dgZNdZCg?filename=screencapture-172-17-0-3-checkout-2020-04-22-12_04_06.png)

*Envio da proposta*

![Envio da proposta](https://ipfs.io/ipfs/QmPRycentHsf9A9X9UMgmz3AiypaWyN6sCr2vhVKdmXA76?filename=screencapture-hml-usevirtus-br-taker-order-28e87d7f-949e-4f9e-aa6b-1c3afb513970-accept-2020-04-22-12_22_44.png)

*Análise pré redirecionamento para o WooCommerce*

![Análise pré redirecionamento para o WooCommerce](https://ipfs.io/ipfs/QmTjDJsdyZ6vT5ebzmryFRByyxjBRf3fRcLZan3666ySgr?filename=screencapture-hml-usevirtus-br-taker-order-28e87d7f-949e-4f9e-aa6b-1c3afb513970-thanks-2020-04-22-12_25_05.png)

*Pedido em processamento*

![Pedido em processamento](https://ipfs.io/ipfs/Qmd9PopaEArk7c4ycaMZZk2ympEaa1qmBDzqSnUbjX6d8K?filename=Captura%20de%20tela%20de%202020-04-22%2012-26-29.png)

== Changelog ==

= 1.0.3 =
* Exclusão de informação obrigatória de renda
* Validação de máscaras de CPF e valores em centavos

= 1.0.3 =
* Selectbox para parcelas
