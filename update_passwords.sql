-- Atualizar senhas com BCrypt válido
UPDATE usuarios SET password = '$2a$11$l4jHscWekWwUUVwK5VQdb.h1KgDYPE/VRRC2dziW5wZUYs8OuIGnC', nivel = 'admin' WHERE email = 'admin@eatsfood.com';
UPDATE usuarios SET password = '$2a$11$5rPgTcAYZVHeHUrW5YopVO.q0TIf2e/EVi1Kf.m9flWU3dCyRzNc2', nivel = 'supervisor' WHERE email = 'supervisor@eatsfood.com';
UPDATE usuarios SET password = '$2a$11$TevRYQVxlEID7Nw97FE5IuLsSmte2me16AmSNkwhhPAK5dDd93dJy', nivel = 'atendente' WHERE email = 'atendente@eatsfood.com';

SELECT email, nivel, LEFT(password, 7) as hash_inicio, LENGTH(password) as tamanho FROM usuarios WHERE email IN ('admin@eatsfood.com', 'supervisor@eatsfood.com', 'atendente@eatsfood.com');
