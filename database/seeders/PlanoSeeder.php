<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Plano;
class PlanoSeeder extends Seeder
{
 public function run()
 {
 $planos = [
 [
 'codigo' => 'basico',
 'nome' => 'Básico',
 'descricao' => 'Plano ideal para pequenos negócios começarem a digitalizar suas operações',
 'valor_mensal' => 99.90,
 'valor_anual' => 999.00,
 'desconto_anual_percent' => 16.67,
 'max_usuarios' => 5,
 'max_produtos' => 100,
 'max_pedidos_mes' => 500,
 'max_filiais' => 1,
 'max_mesas' => 20,
 'max_entregadores' => 5,
 'taxa_transacao_percent' => 2.99,
 'taxa_fixa_pedido' => 0.49,
 'taxa_delivery_percent' => 5.00,
 'recursos' => ['pedidos', 'produtos', 'categorias', 'mesas', 'garcom', 'caixa_basico'],
 'dias_trial' => 30,
 'permite_trial' => true,
 'ativo' => true,
 'ordem' => 1,
 'destaque' => false,
 ],
 [
 'codigo' => 'profissional',
 'nome' => 'Profissional',
 'descricao' => 'Para negócios em crescimento que precisam de mais recursos e funcionalidades',
 'valor_mensal' => 199.90,
 'valor_anual' => 1999.00,
 'desconto_anual_percent' => 16.67,
 'max_usuarios' => 15,
 'max_produtos' => 500,
 'max_pedidos_mes' => 2000,
 'max_filiais' => 3,
 'max_mesas' => 50,
 'max_entregadores' => 15,
 'taxa_transacao_percent' => 2.49,
 'taxa_fixa_pedido' => 0.39,
 'taxa_delivery_percent' => 4.00,
 'recursos' => ['pedidos', 'produtos', 'categorias', 'mesas', 'garcom', 'delivery', 'entregadores', 'clientes', 'relatorios', 'caixa', 'historico_vendas'],
 'dias_trial' => 30,
 'permite_trial' => true,
 'ativo' => true,
 'ordem' => 2,
 'destaque' => true,
 ],
 [
 'codigo' => 'premium',
 'nome' => 'Premium',
 'descricao' => 'Solução completa com recursos avançados para empresas estabelecidas',
 'valor_mensal' => 399.90,
 'valor_anual' => 3999.00,
 'desconto_anual_percent' => 16.67,
 'max_usuarios' => 50,
 'max_produtos' => 2000,
 'max_pedidos_mes' => 10000,
 'max_filiais' => 10,
 'max_mesas' => 200,
 'max_entregadores' => 50,
 'taxa_transacao_percent' => 1.99,
 'taxa_fixa_pedido' => 0.29,
 'taxa_delivery_percent' => 3.00,
 'recursos' => ['pedidos', 'produtos', 'categorias', 'mesas', 'garcom', 'delivery', 'entregadores', 'clientes', 'relatorios', 'relatorios_avancados', 'caixa', 'caixa_multiplo', 'historico_vendas', 'api', 'integracao_ifood', 'integracao_rappi', 'white_label_parcial', 'custom_domain'],
 'dias_trial' => 30,
 'permite_trial' => true,
 'ativo' => true,
 'ordem' => 3,
 'destaque' => false,
 ],
 [
 'codigo' => 'enterprise',
 'nome' => 'Enterprise',
 'descricao' => 'Plano corporativo sem limites com suporte dedicado e personalização completa',
 'valor_mensal' => 999.90,
 'valor_anual' => 9999.00,
 'desconto_anual_percent' => 16.67,
 'max_usuarios' => 999999,
 'max_produtos' => 999999,
 'max_pedidos_mes' => 999999,
 'max_filiais' => 999999,
 'max_mesas' => 999999,
 'max_entregadores' => 999999,
 'taxa_transacao_percent' => 1.49,
 'taxa_fixa_pedido' => 0.19,
 'taxa_delivery_percent' => 2.00,
 'recursos' => ['tudo', 'api_completa', 'white_label_completo', 'custom_domain', 'suporte_dedicado', 'onboarding_personalizado', 'treinamento', 'consultoria', 'prioridade'],
 'dias_trial' => 60,
 'permite_trial' => false,
 'ativo' => true,
 'ordem' => 4,
 'destaque' => false,
 ],
 ];
 foreach ($planos as $plano) {
 Plano::updateOrCreate(
 ['codigo' => $plano['codigo']],
 $plano
 );
 }
 $this->command->info('✅ Planos criados com sucesso!');
 }
}