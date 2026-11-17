# Doce Instante — PIT-II / Sistemas de Informação

Aplicação web de encomendas de cupcakes gourmet desenvolvida para o **Projeto Integrador Transdisciplinar em Sistemas de Informação II** da UNIFRAN.

**Aluno:** Sérgio Gomes Ferreira — **RGM:** 45957312.  
**Repositório:** https://github.com/sgferreira/si_unifran

## Recursos implementados nesta versão inicial

- Catálogo de produtos, pesquisa e filtros por categoria.
- Cadastro, login e logout de clientes, com hash de senha.
- Carrinho de compras persistido no MySQL para clientes autenticados.
- Finalização com endereço de entrega, estoque atualizado em transação e pagamento **inteiramente simulado**, sem cobrança real.
- Histórico e acompanhamento de pedidos.
- Administração de produtos e mudança dos status dos pedidos.
- Design responsivo, proteção CSRF e consultas parametrizadas com PDO.
- Testes unitários para validação de dados, frete e status.

**Não implementados:** pagamento real (PIX/cartões), envio de e-mail, recuperação de senha, notificações, cupons, avaliações, geolocalização, acesso de entregador e hospedagem pública. Tais recursos não devem ser anunciados como prontos.

## Requisitos

Para a opção recomendada, instale **Docker Engine** com **Docker Compose**. Alternativamente use PHP 8.3+, extensão `pdo_mysql`, servidor Apache com `mod_rewrite` e MySQL 8.0+.

## Executar com Docker

1. Clone o projeto: `git clone https://github.com/sgferreira/si_unifran.git`.
2. Entre na pasta: `cd si_unifran`.
3. Copie `.env.example` para `.env` e **altere as senhas de exemplo** (`DB_PASS` e `DB_ROOT_PASS`).
4. Execute `docker compose up --build -d`.
5. Abra **http://localhost:8080** (ou a porta definida por `APP_PORT`).
6. Clique em **Criar conta** para registrar um cliente fictício.

**Importante:** a carga inicial de `database/schema.sql` e `database/seed.sql` ocorre somente quando o volume do MySQL é criado pela primeira vez. Alterações posteriores exigem migração controlada; não apague volumes com dados que deseja preservar.

### Administrador

Após iniciar os contêineres, execute:

```bash
docker compose exec web php scripts/create_admin.php
```

Informe nome, e-mail e senha. Não há credenciais fixas. Depois entre no sistema e acesse `/admin`.

### Testes

```bash
docker compose exec web php tests/UnitTest.php
docker compose exec web sh -c 'find src public scripts tests -name "*.php" -exec php -l {} \;'
```

No ambiente de edição, foram executados **16 testes unitários com sucesso** e foram verificados **32 arquivos PHP sem erros de sintaxe**. **Não houve execução integrada confirmada de PHP + MySQL** neste ambiente. São necessários os testes reais de navegação, carrinho, persistência e pedido em Docker/XAMPP antes de considerar a aplicação validada.

## Organização da aplicação

```text
public/             Entrada HTTP, CSS e JavaScript
src/Core/           Segurança, sessão, conexão e validação
src/Controllers/    Controladores MVC
src/Models/         Acesso e consultas ao banco
src/Services/       Regras de carrinho e pedidos
src/Views/          Telas e layout
database/           Projeto físico MySQL e dados fictícios
scripts/            Criação de administrador pelo terminal
tests/              Testes unitários
docs/               Manual, avaliação de cinco colegas e roteiro de vídeo
```

## Evidências acadêmicas e segurança

- O projeto destina-se **exclusivamente à demonstração acadêmica** e não é um ambiente comercial ou de produção.
- As opções PIX, crédito e débito **não geram cobrança** e não coletam números de cartão.
- Nunca publique `.env`, senhas, tokens nem dados pessoais de colegas.
- Para hospedagem real serão necessários HTTPS, proteção adicional contra abuso, backup, política de privacidade e auditoria de segurança.
- A **Situação 3** exige cinco opiniões reais, registradas em PDF, correções com evidências e vídeo narrado de aproximadamente cinco minutos. Nenhuma opinião ou correção deve ser inventada.

Documentação adicional: [Manual de uso](docs/manual_de_uso.md) · [Plano de testes e formulário dos colegas](docs/plano_de_testes.md) · [Roteiro do vídeo](docs/roteiro_video.md).
