-- Script para criar usuários padrão do call center
-- Execute este script DEPOIS de rodar as migrations

-- Inserir usuário admin padrão
-- Email: admin@eatsfood.com | Senha: admin123
INSERT INTO `usuarios` (`tenant_code`, `ativo`, `nome`, `email`, `role`, `password`, `nivel`, `created_at`) 
VALUES (NULL, 1, 'Administrador', 'admin@eatsfood.com', 'admin', '$2a$11$HASH_AQUI', 'admin', NOW())
ON DUPLICATE KEY UPDATE email = email;

-- Inserir supervisor padrão
-- Email: supervisor@eatsfood.com | Senha: super123
INSERT INTO `usuarios` (`tenant_code`, `ativo`, `nome`, `email`, `role`, `password`, `nivel`, `created_at`) 
VALUES (NULL, 1, 'Supervisor', 'supervisor@eatsfood.com', 'admin', '$2a$11$HASH_AQUI', 'supervisor', NOW())
ON DUPLICATE KEY UPDATE email = email;

-- Inserir atendente padrão
-- Email: atendente@eatsfood.com | Senha: atendente123
INSERT INTO `usuarios` (`tenant_code`, `ativo`, `nome`, `email`, `role`, `password`, `nivel`, `created_at`) 
VALUES (NULL, 1, 'Atendente Teste', 'atendente@eatsfood.com', 'gerente', '$2a$11$HASH_AQUI', 'atendente', NOW())
ON DUPLICATE KEY UPDATE email = email;
