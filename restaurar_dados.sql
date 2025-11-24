USE myd_bar_restaurantes;

-- Criar usuários
INSERT IGNORE INTO usuarios (nome, email, password, tipo, created_at, updated_at) VALUES 
('Administrador', 'admin@restaurante.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', NOW(), NOW()),
('João Silva', 'joao@restaurante.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'garcom', NOW(), NOW()),
('Maria Santos', 'maria@restaurante.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'caixa', NOW(), NOW());

-- Criar mesas
INSERT IGNORE INTO mesas (numero, capacidade, status, disponivel, created_at, updated_at) VALUES 
(1, 4, 'disponivel', 1, NOW(), NOW()),
(2, 2, 'disponivel', 1, NOW(), NOW()),
(3, 6, 'disponivel', 1, NOW(), NOW()),
(4, 4, 'disponivel', 1, NOW(), NOW()),
(5, 4, 'disponivel', 1, NOW(), NOW()),
(6, 2, 'disponivel', 1, NOW(), NOW()),
(7, 6, 'disponivel', 1, NOW(), NOW()),
(8, 4, 'disponivel', 1, NOW(), NOW()),
(9, 4, 'disponivel', 1, NOW(), NOW()),
(10, 8, 'disponivel', 1, NOW(), NOW());

-- Criar categorias
INSERT IGNORE INTO categorias (nome, descricao, ativa, created_at, updated_at) VALUES 
('Bebidas', 'Drinks, sucos e refrigerantes', 1, NOW(), NOW()),
('Lanches', 'Hambúrguers, sanduíches e petiscos', 1, NOW(), NOW()),
('Pratos Principais', 'Pratos executivos e refeições', 1, NOW(), NOW()),
('Sobremesas', 'Doces e sobremesas variadas', 1, NOW(), NOW()),
('Entradas', 'Aperitivos e entradas', 1, NOW(), NOW());

-- Criar produtos
INSERT IGNORE INTO produtos (nome, preco, categoria, descricao, ativo, created_at, updated_at) VALUES 
('Hambúrguer Artesanal', 25.90, 'Lanches', 'Pão artesanal, carne 150g, queijo, alface e tomate', 1, NOW(), NOW()),
('Pizza Margherita', 32.50, 'Pratos Principais', 'Molho de tomate, mussarela e manjericão', 1, NOW(), NOW()),
('Coca-Cola 350ml', 5.50, 'Bebidas', 'Refrigerante gelado', 1, NOW(), NOW()),
('Suco de Laranja', 8.00, 'Bebidas', 'Suco natural da fruta', 1, NOW(), NOW()),
('Batata Frita', 12.00, 'Entradas', 'Porção de batata frita crocante', 1, NOW(), NOW()),
('Cerveja Heineken', 8.50, 'Bebidas', 'Cerveja long neck gelada', 1, NOW(), NOW()),
('Pudim de Leite', 9.90, 'Sobremesas', 'Pudim caseiro com calda', 1, NOW(), NOW()),
('Filé à Parmegiana', 45.00, 'Pratos Principais', 'Filé empanado com molho e queijo', 1, NOW(), NOW()),
('Salada Caesar', 18.50, 'Entradas', 'Alface, croutons, parmesão e molho caesar', 1, NOW(), NOW()),
('Água Mineral', 3.00, 'Bebidas', 'Água sem gás 500ml', 1, NOW(), NOW());
