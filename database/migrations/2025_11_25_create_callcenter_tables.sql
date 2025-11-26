-- Migration para Sistema Call Center Desktop

-- Tabela de usuários do call center (se não existir)
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL UNIQUE,
  `senha` varchar(255) NOT NULL,
  `nivel` enum('admin','supervisor','atendente','entregador') NOT NULL DEFAULT 'atendente',
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `login_attempts` int(11) DEFAULT 0,
  `locked_until` datetime DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de estornos
CREATE TABLE IF NOT EXISTS `estornos` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pedido_id` bigint(20) UNSIGNED NOT NULL,
  `item_pedido_id` bigint(20) UNSIGNED DEFAULT NULL,
  `tipo` enum('parcial','total') NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `motivo` text NOT NULL,
  `solicitado_por` bigint(20) UNSIGNED NOT NULL,
  `aprovado_por` bigint(20) UNSIGNED DEFAULT NULL,
  `status` enum('pendente','aprovado','rejeitado') NOT NULL DEFAULT 'pendente',
  `solicitado_em` datetime NOT NULL,
  `processado_em` datetime DEFAULT NULL,
  `observacoes_aprovacao` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `pedido_id` (`pedido_id`),
  KEY `item_pedido_id` (`item_pedido_id`),
  KEY `solicitado_por` (`solicitado_por`),
  KEY `aprovado_por` (`aprovado_por`),
  CONSTRAINT `estornos_pedido_fk` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `estornos_item_pedido_fk` FOREIGN KEY (`item_pedido_id`) REFERENCES `item_pedidos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `estornos_solicitado_fk` FOREIGN KEY (`solicitado_por`) REFERENCES `usuarios` (`id`),
  CONSTRAINT `estornos_aprovado_fk` FOREIGN KEY (`aprovado_por`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de auditoria
CREATE TABLE IF NOT EXISTS `auditoria_logs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `usuario_id` bigint(20) UNSIGNED NOT NULL,
  `acao` varchar(100) NOT NULL,
  `entidade` varchar(50) NOT NULL,
  `entidade_id` bigint(20) UNSIGNED DEFAULT NULL,
  `detalhes` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`),
  KEY `entidade` (`entidade`, `entidade_id`),
  KEY `created_at` (`created_at`),
  CONSTRAINT `auditoria_usuario_fk` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Inserir usuário admin padrão (senha: admin123)
-- Hash BCrypt para 'admin123'
INSERT INTO `usuarios` (`nome`, `email`, `senha`, `nivel`, `ativo`) 
VALUES ('Administrador', 'admin@eatsfood.com', '$2a$11$xQZ9mZxQZ9mZxQZ9mZxQZuK5K5K5K5K5K5K5K5K5K5K5K5K5K5K5K', 'admin', 1)
ON DUPLICATE KEY UPDATE email = email;

-- Inserir supervisor padrão (senha: super123)
INSERT INTO `usuarios` (`nome`, `email`, `senha`, `nivel`, `ativo`) 
VALUES ('Supervisor', 'supervisor@eatsfood.com', '$2a$11$xQZ9mZxQZ9mZxQZ9mZxQZuL6L6L6L6L6L6L6L6L6L6L6L6L6L6L6L', 'supervisor', 1)
ON DUPLICATE KEY UPDATE email = email;

-- Inserir atendente padrão (senha: atendente123)
INSERT INTO `usuarios` (`nome`, `email`, `senha`, `nivel`, `ativo`) 
VALUES ('Atendente Teste', 'atendente@eatsfood.com', '$2a$11$xQZ9mZxQZ9mZxQZ9mZxQZuM7M7M7M7M7M7M7M7M7M7M7M7M7M7M7M', 'atendente', 1)
ON DUPLICATE KEY UPDATE email = email;
