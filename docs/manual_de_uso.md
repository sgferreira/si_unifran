# Manual de uso — Sistema Doce Instante

**Projeto:** Doce Instante — Cupcakes Gourmet Delivery  
**Disciplina:** Projeto Integrador Transdisciplinar em Sistemas de Informação II  
**Aluno:** Sérgio Gomes Ferreira — RGM 45957312

## Acesso inicial
1. Siga a instalação descrita no [README](../README.md).
2. Abra `http://localhost:8080`.
3. A página inicial apresenta o cardápio demonstrativo com preços e estoques fictícios.

## Cliente
1. Clique em **Criar conta** e preencha nome, e-mail e uma senha de pelo menos oito caracteres.
2. Use **Entrar** com os dados cadastrados. Visitantes podem consultar produtos, mas precisam se autenticar para usar o carrinho.
3. Localize produtos pelo nome ou selecione uma categoria e clique em **Buscar**.
4. Clique no botão **+** de um produto para adicioná-lo ao carrinho.
5. Em **Meu carrinho**, altere a quantidade e clique em **Atualizar**, ou remova o item. Quantidade zero remove o produto.
6. Clique em **Finalizar pedido** e informe dados fictícios de endereço para os testes acadêmicos.
7. Selecione PIX, crédito ou débito **simulado**; nenhuma cobrança real será efetuada.
8. Clique em **Confirmar pedido de demonstração**. O pedido recebe o status **Confirmado** e o estoque é reduzido.
9. Consulte **Meus pedidos** e **Ver detalhes** para acompanhar o histórico.

## Administrador
1. Crie um usuário administrativo pelo script de terminal indicado no README.
2. Entre com a conta administrativa e acesse **Administração**.
3. Na aba **Pedidos**, avance a situação de **Confirmado** para **Em preparo**, depois **Saiu para entrega** e **Entregue**.
4. Na aba **Produtos**, cadastre um sabor, altere preço ou estoque e desmarque **Ativo** para ocultá-lo do catálogo.

## Limitações conhecidas
- Não há integração de pagamento real, e-mail, mapas ou notificações.
- Não há recuperação de senha por e-mail.
- A taxa de entrega demonstrativa é R$ 8,00, gratuita a partir de R$ 80,00 de subtotal.
- A aplicação não foi homologada para uso comercial ou com dados pessoais reais.
- A execução integrada, os cinco testes externos e a hospedagem pública precisam ser verificados em ambiente adequado.
