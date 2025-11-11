-- Script SQL para criar um pedido com observações de teste
INSERT INTO pedidos (usuario_id, mesa_id, total, status, observacoes, created_at, updated_at) 
VALUES (1, 1, 50.00, 'finalizado', 'Pedido para teste de observações', NOW(), NOW());

-- Pegar o ID do último pedido inserido e criar itens com observações
SET @pedido_id = LAST_INSERT_ID();

INSERT INTO item_pedidos (pedido_id, produto_id, quantidade, preco_unitario, subtotal, observacoes, created_at, updated_at) 
VALUES 
(@pedido_id, 1, 1, 25.00, 25.00, 'Mal passado, sem cebola', NOW(), NOW()),
(@pedido_id, 2, 2, 12.50, 25.00, 'Sem açúcar, com gelo', NOW(), NOW());
