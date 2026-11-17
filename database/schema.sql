-- UNIFRAN | PIT-II | Doce Instante | Projeto fisico MySQL 8.0+
CREATE DATABASE IF NOT EXISTS doce_instante CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE doce_instante;

CREATE TABLE usuarios (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 nome VARCHAR(150) NOT NULL,
 email VARCHAR(190) NOT NULL UNIQUE,
 senha_hash VARCHAR(255) NOT NULL,
 perfil ENUM('CLIENTE','ADMIN','ENTREGADOR') NOT NULL DEFAULT 'CLIENTE',
 ativo BOOLEAN NOT NULL DEFAULT TRUE,
 criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 atualizado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE enderecos (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 usuario_id BIGINT UNSIGNED NOT NULL,
 cep CHAR(8) NOT NULL,
 logradouro VARCHAR(180) NOT NULL,
 numero VARCHAR(20) NOT NULL,
 complemento VARCHAR(100), bairro VARCHAR(100) NOT NULL,
 cidade VARCHAR(100) NOT NULL, uf CHAR(2) NOT NULL,
 referencia VARCHAR(180), principal BOOLEAN NOT NULL DEFAULT FALSE,
 CONSTRAINT fk_end_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
 INDEX idx_end_usuario (usuario_id)
) ENGINE=InnoDB;

CREATE TABLE categorias (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 nome VARCHAR(100) NOT NULL UNIQUE,
 descricao VARCHAR(255), ativa BOOLEAN NOT NULL DEFAULT TRUE
) ENGINE=InnoDB;

CREATE TABLE produtos (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 categoria_id BIGINT UNSIGNED NOT NULL,
 nome VARCHAR(150) NOT NULL,
 descricao TEXT,
 preco DECIMAL(10,2) NOT NULL,
 estoque INT UNSIGNED NOT NULL DEFAULT 0,
 imagem_url VARCHAR(500), ativo BOOLEAN NOT NULL DEFAULT TRUE,
 criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 atualizado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 CONSTRAINT chk_prod_preco CHECK (preco >= 0),
 CONSTRAINT fk_prod_categoria FOREIGN KEY (categoria_id) REFERENCES categorias(id),
 INDEX idx_prod_categoria_ativo (categoria_id, ativo),
 INDEX idx_prod_nome (nome)
) ENGINE=InnoDB;

CREATE TABLE carrinhos (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 usuario_id BIGINT UNSIGNED NOT NULL,
 situacao ENUM('ABERTO','CONVERTIDO','ABANDONADO') NOT NULL DEFAULT 'ABERTO',
 criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_car_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
 INDEX idx_car_usuario_situacao (usuario_id, situacao)
) ENGINE=InnoDB;

CREATE TABLE carrinho_itens (
 carrinho_id BIGINT UNSIGNED NOT NULL,
 produto_id BIGINT UNSIGNED NOT NULL,
 quantidade INT UNSIGNED NOT NULL,
 PRIMARY KEY (carrinho_id, produto_id),
 CONSTRAINT chk_car_quant CHECK (quantidade > 0),
 CONSTRAINT fk_ci_carrinho FOREIGN KEY (carrinho_id) REFERENCES carrinhos(id) ON DELETE CASCADE,
 CONSTRAINT fk_ci_produto FOREIGN KEY (produto_id) REFERENCES produtos(id)
) ENGINE=InnoDB;

CREATE TABLE pedidos (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 cliente_id BIGINT UNSIGNED NOT NULL,
 endereco_id BIGINT UNSIGNED NOT NULL,
 carrinho_id BIGINT UNSIGNED NULL UNIQUE,
 status ENUM('AGUARDANDO_PAGAMENTO','CONFIRMADO','EM_PREPARO','SAIU_PARA_ENTREGA','ENTREGUE','CANCELADO') NOT NULL DEFAULT 'AGUARDANDO_PAGAMENTO',
 subtotal DECIMAL(10,2) NOT NULL,
 taxa_entrega DECIMAL(10,2) NOT NULL DEFAULT 0,
 desconto DECIMAL(10,2) NOT NULL DEFAULT 0,
 total DECIMAL(10,2) GENERATED ALWAYS AS (subtotal + taxa_entrega - desconto) STORED,
 observacoes VARCHAR(500),
 criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 atualizado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 CONSTRAINT chk_pedido_valores CHECK (subtotal >= 0 AND taxa_entrega >= 0 AND desconto >= 0 AND desconto <= subtotal + taxa_entrega),
 CONSTRAINT fk_ped_cliente FOREIGN KEY (cliente_id) REFERENCES usuarios(id),
 CONSTRAINT fk_ped_endereco FOREIGN KEY (endereco_id) REFERENCES enderecos(id),
 CONSTRAINT fk_ped_carrinho FOREIGN KEY (carrinho_id) REFERENCES carrinhos(id),
 INDEX idx_ped_cliente_data (cliente_id, criado_em),
 INDEX idx_ped_status (status)
) ENGINE=InnoDB;

CREATE TABLE pedido_itens (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 pedido_id BIGINT UNSIGNED NOT NULL,
 produto_id BIGINT UNSIGNED NOT NULL,
 nome_produto VARCHAR(150) NOT NULL,
 quantidade INT UNSIGNED NOT NULL,
 preco_unitario DECIMAL(10,2) NOT NULL,
 total_item DECIMAL(10,2) GENERATED ALWAYS AS (quantidade * preco_unitario) STORED,
 CONSTRAINT chk_pi_valores CHECK (quantidade > 0 AND preco_unitario >= 0),
 CONSTRAINT fk_pi_pedido FOREIGN KEY (pedido_id) REFERENCES pedidos(id),
 CONSTRAINT fk_pi_produto FOREIGN KEY (produto_id) REFERENCES produtos(id),
 INDEX idx_pi_pedido (pedido_id)
) ENGINE=InnoDB;

CREATE TABLE pagamentos (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 pedido_id BIGINT UNSIGNED NOT NULL,
 metodo ENUM('PIX','CARTAO_CREDITO','CARTAO_DEBITO') NOT NULL,
 status ENUM('PENDENTE','APROVADO','RECUSADO','ESTORNADO') NOT NULL DEFAULT 'PENDENTE',
 valor DECIMAL(10,2) NOT NULL,
 referencia_gateway VARCHAR(190) UNIQUE,
 criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 confirmado_em DATETIME NULL,
 CONSTRAINT chk_pag_valor CHECK (valor > 0),
 CONSTRAINT fk_pag_pedido FOREIGN KEY (pedido_id) REFERENCES pedidos(id),
 INDEX idx_pag_pedido_status (pedido_id, status)
) ENGINE=InnoDB;

CREATE TABLE entregas (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 pedido_id BIGINT UNSIGNED NOT NULL UNIQUE,
 entregador_id BIGINT UNSIGNED NULL,
 status ENUM('PENDENTE','ATRIBUIDA','EM_ROTA','ENTREGUE','FALHOU') NOT NULL DEFAULT 'PENDENTE',
 atribuida_em DATETIME NULL, saiu_em DATETIME NULL, entregue_em DATETIME NULL,
 observacoes VARCHAR(500),
 CONSTRAINT fk_ent_pedido FOREIGN KEY (pedido_id) REFERENCES pedidos(id),
 CONSTRAINT fk_ent_entregador FOREIGN KEY (entregador_id) REFERENCES usuarios(id),
 INDEX idx_ent_entregador_status (entregador_id, status)
) ENGINE=InnoDB;

CREATE TABLE historico_status_pedido (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 pedido_id BIGINT UNSIGNED NOT NULL,
 status_anterior VARCHAR(40), status_novo VARCHAR(40) NOT NULL,
 alterado_por BIGINT UNSIGNED NULL,
 alterado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_hsp_pedido FOREIGN KEY (pedido_id) REFERENCES pedidos(id),
 CONSTRAINT fk_hsp_usuario FOREIGN KEY (alterado_por) REFERENCES usuarios(id),
 INDEX idx_hsp_pedido_data (pedido_id, alterado_em)
) ENGINE=InnoDB;

CREATE TABLE consentimentos_marketing (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 usuario_id BIGINT UNSIGNED NOT NULL,
 consentiu BOOLEAN NOT NULL,
 registrado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 origem VARCHAR(80) NOT NULL,
 CONSTRAINT fk_cm_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
 INDEX idx_cm_usuario_data (usuario_id, registrado_em)
) ENGINE=InnoDB;

INSERT INTO categorias (nome, descricao) VALUES
 ('Cupcakes','Cupcakes gourmet'),('Combos','Combinações promocionais'),('Bebidas','Bebidas para acompanhar os doces');

-- Regras transacionais na aplicacao/servico:
-- 1) Bloquear linhas de produtos com SELECT ... FOR UPDATE ao confirmar o pedido.
-- 2) Validar e decrementar estoque na mesma transacao, revertendo em caso de falha.
-- 3) Calcular subtotal no servidor e validar totais.
-- 4) Garantir endereco do cliente e entregador com perfil correto.
-- 5) Validar transicoes de status e registrar historico.
-- 6) Gateway de pagamento requer autenticidade e idempotencia (nao implementado).
-- 7) Nao armazenar dados completos de cartao.
-- 8) Hash de senha, HTTPS, controle de acesso e backups.
