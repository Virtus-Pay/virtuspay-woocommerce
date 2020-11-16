=== VirtusPay Boleto Parcelado ===
Contributors: paulosouzainfo
Tags: woocommerce, gateway, payments, installment billet
Requires at least: 5.1
Tested up to: 5.4.1
Requires PHP: 7.2
Stable tag: 2.1.2
License: MIT
License URI: https://choosealicense.com/licenses/mit

Pagamentos para o WooCommerce de boletos parcelados através da VirtusPay.

== Descrição ==

A VirtusPay possibilita o parcelamento no boleto online, sem precisar de um cartão de crédito!

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
  * Automação de status de propostas para pagamentos abertos, em processamento, recusados ou cancelados e concluídos;
  * Cancelamento total e parcial de compras.

Boas vendas!

== Instalação ==

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

== Sobre o plugin ==

Este plugin para o WooCommerce permite a configuração e apresentação de um novo meio de pagamento para ser escolhido no momento do checkout e usa integrações externas para validação de dados e processamento de pedidos através de uma API sobre a responsabilidade da VirtusPay.

== Changelog ==

= 2.1.2 =
* Atualização do checkout

= 2.1.1 =
* Requisição direta para a API na tela de pagamento do pedido
* Campo de retorno para desistência no nosso checkout para criação da proposta

= 2.1.0 =
* Comunicação e transparência com o cliente

= 2.0.0 =
* Refactoring com remoção de tipagens dinâmicas para compatibilidade em múltiplas versões do PHP

= 1.3.14 =
* Recupera o valor do carrinho não parseado pelo WC

= 1.2.14 =
* Retorno de string "OK" para callback no endpoint Woocommerce
* Alteração de status na atualização de pedidos

= 1.2.13 =
* Destaque para a palavra "Boleto Parcelado"

= 1.2.12 =
* Conflitos de nomenclatura para pastas
* Tentativa de processamento de parcelas pelo cliente

= 1.2.11 =
* Fallback de apresentação de parcelas em select
* Acerto de link de consulta de contas através do token 

= 1.2.10 =
* Retirada as instruções de instalação através de arquivos .zip

= 1.2.9 =
* Path para o nome da pasta do plugin

= 1.2.8 =
* Definição de caminho absoluto do plugin no WP

= 1.2.7 =
* Tratamento de retorno nulo convertido para string ao recuperar as opções de configurações

= 1.2.6 =
* Ordem reversa para seleção de parcelas

= 1.2.5 =
* Ordem reversa para seleção de parcelas
* Seleção automática de parcelas pelo maior número disponível

= 1.1.5 =
* Definição de cache pela URL de JS e CSS 

= 1.1.4 =
* Solução de bugs para seleção de parcelas através da VirtusPay
* Cancelamento parcial de compras com estorno automático para o cliente

= 1.0.4 =
* Selectbox para parcelas
* Remapeamento de nomes de classes, métodos, funções e variáveis
* Sanitização, escape e validação de dados de entrada

= 1.0.3 =
* Exclusão de informação obrigatória de renda
* Validação de máscaras de CPF e valores em centavos
