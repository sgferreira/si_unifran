# Plano de testes — PIT-II / Situação-Problema 3

Este documento define os procedimentos de teste; **não representa avaliações já realizadas com colegas**.

## Pré-condições
Aplicação instalada, banco inicializado, endereço acessível ao avaliador, administrador criado e produtos demonstrativos ativos. Use contas, endereços e informações fictícias.

| Código | O que testar | Resultado esperado |
|---|---|---|
| CT01 | Abrir página inicial | Cardápio e preços exibidos |
| CT02 | Buscar “chocolate” | Itens correspondentes exibidos |
| CT03 | Filtrar categoria | Apenas a categoria selecionada |
| CT04 | Cadastrar cliente válido | Conta criada e sessão iniciada |
| CT05 | Cadastrar e-mail repetido | Mensagem amigável, sem duplicidade |
| CT06 | Entrar com senha incorreta | Acesso negado |
| CT07 | Adicionar ao carrinho sem login | Redirecionamento para login |
| CT08 | Adicionar item com login | Item salvo e total atualizado |
| CT09 | Alterar e remover item | Carrinho atualizado |
| CT10 | Quantidade acima do estoque | Operação recusada |
| CT11 | Finalizar com CEP inválido | Validação impede o pedido |
| CT12 | Finalizar pedido válido | Pedido gravado e estoque reduzido |
| CT13 | Abrir “Meus pedidos” | Somente pedidos do usuário |
| CT14 | Acessar administração como cliente | Acesso negado |
| CT15 | Avançar status como administrador | Histórico e status atualizados |
| CT16 | Cadastrar, editar e desativar produto | Catálogo reflete alterações |
| CT17 | Comprar o último item e repetir compra | Falta de estoque impede pedido |
| CT18 | Layout em celular | Menu, formulários e cards utilizáveis |

## Modelo individual de avaliação — copiar cinco vezes

**Nome de quem testou:** __________________________________

**Data:** ____/____/______ 

**Navegador e dispositivo:** __________________________________

**Endereço/versão testada:** __________________________________

**1. Funcionalidades testadas e aprovadas:** descrever cada funcionalidade que funcionou, citando o CT correspondente.

________________________________________________________________

**2. Funcionalidades testadas com falha:** descrever o que não funcionou, como reproduzir, resultado esperado e o que deve ser corrigido.

________________________________________________________________

**3. Funcionalidades não testadas e motivo:** indicar recursos que faltaram ou que existiam, mas não foram utilizados.

________________________________________________________________

**4. Sugestões de melhoria:** __________________________________

**5. Nota de avaliação geral (1–5) e comentários:** __________________________

## Registro de correções

| ID | Avaliador | Falha ou sugestão | Prioridade | Correção | Evidência antes/depois | Reteste | Estado |
|---|---|---|---|---|---|---|---|
| — | A preencher após testes reais | — | — | — | — | — | Pendente |

## Laudo de qualidade
Preencher apenas depois dos testes efetivos: versão, ambiente, casos aprovados/reprovados/não executados, não conformidades, correções, capturas de tela, retestes e conclusão.

As **cinco opiniões reais** devem ser reunidas em um único PDF. Não registrar dados inventados.
