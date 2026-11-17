-- Dados fictícios para demonstração. Executado somente no primeiro start do MySQL.
USE doce_instante;

INSERT INTO produtos (categoria_id, nome, descricao, preco, estoque) VALUES
((SELECT id FROM categorias WHERE nome='Cupcakes'), 'Red Velvet', 'Massa vermelha macia, cobertura de cream cheese e toque de baunilha.', 16.90, 30),
((SELECT id FROM categorias WHERE nome='Cupcakes'), 'Chocolate Belga', 'Chocolate intenso com ganache cremosa e raspas delicadas.', 17.90, 25),
((SELECT id FROM categorias WHERE nome='Cupcakes'), 'Morango Encantado', 'Baunilha amanteigada, recheio de morango e chantilly leve.', 15.90, 28),
((SELECT id FROM categorias WHERE nome='Cupcakes'), 'Pistache dos Sonhos', 'Massa de pistache com creme suave e crocante especial.', 19.90, 18),
((SELECT id FROM categorias WHERE nome='Cupcakes'), 'Doce de Leite', 'Cupcake de baunilha com doce de leite e caramelo.', 15.90, 32),
((SELECT id FROM categorias WHERE nome='Cupcakes'), 'Limão Siciliano', 'Massa cítrica e cobertura delicada de merengue.', 16.90, 20),
((SELECT id FROM categorias WHERE nome='Combos'), 'Caixa de 4 Cupcakes', 'Seleção especial de quatro cupcakes da casa.', 59.90, 15),
((SELECT id FROM categorias WHERE nome='Combos'), 'Caixa Celebração', 'Seis cupcakes gourmet para presentear ou compartilhar.', 84.90, 12),
((SELECT id FROM categorias WHERE nome='Bebidas'), 'Café Especial', 'Café filtrado com aroma suave, servido quentinho.', 8.90, 40),
((SELECT id FROM categorias WHERE nome='Bebidas'), 'Chocolate Quente', 'Bebida cremosa de chocolate com leite.', 12.90, 25);
