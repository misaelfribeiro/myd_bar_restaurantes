#!/usr/bin/env node

/**
 * Script de Teste Rápido do App MyD
 * Execute: node teste-rapido.js
 */

const http = require('http');

console.log('\n🧪 TESTE RÁPIDO - MyD Bar & Restaurantes\n');
console.log('='  .repeat(50));

const tests = [
    {
        name: 'Servidor Web',
        url: 'http://localhost',
        checkPath: '/'
    },
    {
        name: 'App Cliente',
        url: 'http://localhost/app-cliente/',
        checkPath: '/app-cliente/index.html'
    },
    {
        name: 'Service Worker',
        url: 'http://localhost/app-cliente/',
        checkPath: '/app-cliente/service-worker.js'
    },
    {
        name: 'Manifest PWA',
        url: 'http://localhost/app-cliente/',
        checkPath: '/app-cliente/manifest.json'
    },
    {
        name: 'Ícones',
        url: 'http://localhost/app-cliente/',
        checkPath: '/app-cliente/icons/icon-192x192.png'
    },
    {
        name: 'API de Pedidos',
        url: 'http://localhost/api/app/',
        checkPath: '/api/app/pedidos'
    }
];

let testsPassed = 0;

function testConnection(test, index) {
    return new Promise((resolve) => {
        const path = test.checkPath || '/';
        const url = new URL(test.url);
        
        const options = {
            hostname: url.hostname,
            port: url.port || 80,
            path: path,
            method: 'GET',
            timeout: 3000
        };
        
        const req = http.request(options, (res) => {
            if (res.statusCode >= 200 && res.statusCode < 400) {
                console.log(`✅ ${test.name.padEnd(25)} [${res.statusCode}]`);
                testsPassed++;
            } else {
                console.log(`⚠️  ${test.name.padEnd(25)} [${res.statusCode}]`);
            }
            resolve();
        });
        
        req.on('error', (err) => {
            console.log(`❌ ${test.name.padEnd(25)} [${err.code}]`);
            resolve();
        });
        
        req.on('timeout', () => {
            console.log(`⏱️  ${test.name.padEnd(25)} [TIMEOUT]`);
            req.destroy();
            resolve();
        });
        
        req.end();
    });
}

async function runTests() {
    for (let i = 0; i < tests.length; i++) {
        await testConnection(tests[i], i);
    }
    
    console.log('\n' + '='.repeat(50));
    console.log(`\nResultado: ${testsPassed}/${tests.length} testes passaram\n`);
    
    if (testsPassed === tests.length) {
        console.log('🎉 Tudo funcionando! App pronto para instalar como PWA.\n');
        console.log('📱 Acesse: http://localhost/app-cliente/');
    } else {
        console.log('⚠️  Alguns testes falharam. Verifique o servidor Laravel.\n');
        console.log('💡 Dicas:');
        console.log('1. Verifique se o servidor Laravel está rodando');
        console.log('2. Execute: php artisan serve');
        console.log('3. Configure XAMPP corretamente\n');
    }
}

runTests().catch(console.error);
