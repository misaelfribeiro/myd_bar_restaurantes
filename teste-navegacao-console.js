// Cole este código no console do navegador para testar:

console.log('=== TESTE DE NAVEGAÇÃO ===');

// Verificar se funções existem
console.log('showCart existe?', typeof showCart);
console.log('proceedToCheckout existe?', typeof proceedToCheckout);

// Testar showCart diretamente
if (typeof showCart === 'function') {
    console.log('✅ Testando showCart()...');
    showCart();
} else {
    console.error('❌ showCart não encontrado');
}

// Aguardar 2 segundos e testar proceedToCheckout
setTimeout(() => {
    if (typeof proceedToCheckout === 'function') {
        console.log('✅ Testando proceedToCheckout()...');
        proceedToCheckout();
    } else {
        console.error('❌ proceedToCheckout não encontrado');
    }
}, 2000);
