// Teste de validação do carrinho
console.clear();

// Simular appState
window.appState = window.appState || {};
window.appState.cart = [];
window.appState.restaurants = [
    {tenant_code: 'RESTAURANTE0001', nome_fantasia: 'RESTAURANTE TESTE'},
    {tenant_code: 'RESTAURA0003', nome_fantasia: 'Restaurante da Dona Claudia'}
];

// Produto 1 - Restaurante Teste
window.appState.products = [
    {id: 1, nome: 'Coca-Cola', preco: 5.00, ativo: true, tenant_code: 'RESTAURANTE0001'},
    {id: 2, nome: 'Hambúrguer', preco: 35.00, ativo: true, tenant_code: 'RESTAURA0003'}
];

console.log(' TESTE: Adicionando produto do Restaurante Teste...');
// Simular adição - você deve ver a validação funcionando no app real

console.log(' Para testar no app:');
console.log('1. Adicione Coca-Cola (RESTAURANTE TESTE)');
console.log('2. Tente adicionar Hambúrguer (Dona Claudia)');
console.log('3. Deve aparecer o alerta de confirmação');
