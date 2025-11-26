-- Alterar tabela usuarios para call center
-- Adicionar colunas necessárias

ALTER TABLE `usuarios` 
  ADD COLUMN IF NOT EXISTS `nivel` enum('admin','supervisor','atendente','entregador') NOT NULL DEFAULT 'atendente' AFTER `role`,
  ADD COLUMN IF NOT EXISTS `login_attempts` int(11) DEFAULT 0 AFTER `nivel`,
  ADD COLUMN IF NOT EXISTS `locked_until` datetime DEFAULT NULL AFTER `login_attempts`,
  ADD COLUMN IF NOT EXISTS `last_login` datetime DEFAULT NULL AFTER `locked_until`;

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
