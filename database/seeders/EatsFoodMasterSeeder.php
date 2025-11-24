<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Empresa;
class EatsFoodMasterSeeder extends Seeder
{
 public function run()
 {
 $master = Empresa::where('is_master', true)->first();
 if ($master) {
 $this->command->info('✅ Empresa Master EatsFood já existe!');
 return;
 }
 $eatsfood = Empresa::create([
 'nome_fantasia' => 'EatsFood',
 'razao_social' => 'EatsFood Tecnologia em Alimentação LTDA',
 'cnpj' => '12345678000190',
 'inscricao_estadual' => '123.456.789.012',
 'inscricao_municipal' => '987654',
 'telefone' => '11999999999',
 'celular' => '11988888888',
 'email' => 'contato@eatsfood.com.br',
 'site' => 'https://eatsfood.com.br',
 'endereco_rua' => 'Avenida Paulista',
 'endereco_numero' => '1000',
 'endereco_complemento' => 'Conjunto 100',
 'endereco_bairro' => 'Bela Vista',
 'endereco_cidade' => 'São Paulo',
 'endereco_estado' => 'SP',
 'endereco_cep' => '01310100',
 'latitude' => -23.5505199,
 'longitude' => -46.6333094,
 'tipo' => 'matriz',
 'descricao' => 'Plataforma líder em tecnologia para gestão de restaurantes, bares e delivery.',
 'horario_abertura' => '00:00:00',
 'horario_fechamento' => '23:59:59',
 'dias_funcionamento' => ['seg', 'ter', 'qua', 'qui', 'sex', 'sab', 'dom'],
 'aceita_delivery' => true,
 'taxa_entrega_padrao' => 0.00,
 'ativo' => true,
 'is_master' => true,
 'tenant_code' => 'EATSFOOD',
 'plano' => 'enterprise',
 'data_inicio_contrato' => now(),
 'status_contrato' => 'ativo',
 'max_usuarios' => 999999,
 'max_produtos' => 999999,
 'max_pedidos_mes' => 999999,
 'max_filiais' => 999999,
 'valor_mensalidade' => 0.00,
 'taxa_transacao_percent' => 0.00,
 'taxa_fixa_pedido' => 0.00,
 'dominio_personalizado' => 'eatsfood.com.br',
 'cor_primaria' => '#6366f1',
 'cor_secundaria' => '#8b5cf6',
 'permite_white_label' => true,
 'recursos_habilitados' => ['delivery', 'mesas', 'caixa', 'produtos', 'pedidos', 'garcom', 'entregadores', 'clientes', 'relatorios_basicos', 'relatorios_avancados', 'api', 'app_mobile', 'admin_plataforma']
 ]);
 $this->command->info('✅ Empresa Master EatsFood criada com sucesso!');
 $this->command->info('📧 Email: ' . $eatsfood->email);
 $this->command->info('🔑 Tenant Code: ' . $eatsfood->tenant_code);
 }
}