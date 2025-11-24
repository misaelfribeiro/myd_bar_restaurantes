<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Permissao;
class PermissoesSeeder extends Seeder
{
 public function run()
 {
 $permissoes = [
 ['nome' => 'Visualizar Dashboard', 'slug' => 'dashboard.view', 'modulo' => 'Dashboard', 'descricao' => 'Ver painel principal'],
 ['nome' => 'Visualizar Pedidos', 'slug' => 'pedidos.view', 'modulo' => 'Pedidos', 'descricao' => 'Ver lista de pedidos'],
 ['nome' => 'Criar Pedidos', 'slug' => 'pedidos.create', 'modulo' => 'Pedidos', 'descricao' => 'Criar novos pedidos'],
 ['nome' => 'Editar Pedidos', 'slug' => 'pedidos.edit', 'modulo' => 'Pedidos', 'descricao' => 'Editar pedidos existentes'],
 ['nome' => 'Cancelar Pedidos', 'slug' => 'pedidos.cancel', 'modulo' => 'Pedidos', 'descricao' => 'Cancelar pedidos'],
 ['nome' => 'Visualizar Produtos', 'slug' => 'produtos.view', 'modulo' => 'Produtos', 'descricao' => 'Ver lista de produtos'],
 ['nome' => 'Criar Produtos', 'slug' => 'produtos.create', 'modulo' => 'Produtos', 'descricao' => 'Cadastrar novos produtos'],
 ['nome' => 'Editar Produtos', 'slug' => 'produtos.edit', 'modulo' => 'Produtos', 'descricao' => 'Editar produtos'],
 ['nome' => 'Excluir Produtos', 'slug' => 'produtos.delete', 'modulo' => 'Produtos', 'descricao' => 'Excluir produtos'],
 ['nome' => 'Visualizar Categorias', 'slug' => 'categorias.view', 'modulo' => 'Categorias', 'descricao' => 'Ver categorias'],
 ['nome' => 'Gerenciar Categorias', 'slug' => 'categorias.manage', 'modulo' => 'Categorias', 'descricao' => 'Criar, editar e excluir categorias'],
 ['nome' => 'Visualizar Mesas', 'slug' => 'mesas.view', 'modulo' => 'Mesas', 'descricao' => 'Ver mesas'],
 ['nome' => 'Gerenciar Mesas', 'slug' => 'mesas.manage', 'modulo' => 'Mesas', 'descricao' => 'Criar, editar e excluir mesas'],
 ['nome' => 'Visualizar Caixa', 'slug' => 'caixa.view', 'modulo' => 'Caixa', 'descricao' => 'Ver caixa'],
 ['nome' => 'Abrir Caixa', 'slug' => 'caixa.open', 'modulo' => 'Caixa', 'descricao' => 'Abrir caixa'],
 ['nome' => 'Fechar Caixa', 'slug' => 'caixa.close', 'modulo' => 'Caixa', 'descricao' => 'Fechar caixa'],
 ['nome' => 'Relatórios Caixa', 'slug' => 'caixa.reports', 'modulo' => 'Caixa', 'descricao' => 'Ver relatórios do caixa'],
 ['nome' => 'Visualizar Clientes', 'slug' => 'clientes.view', 'modulo' => 'Clientes', 'descricao' => 'Ver clientes'],
 ['nome' => 'Gerenciar Clientes', 'slug' => 'clientes.manage', 'modulo' => 'Clientes', 'descricao' => 'Criar, editar e excluir clientes'],
 ['nome' => 'Visualizar Funcionários', 'slug' => 'funcionarios.view', 'modulo' => 'RH', 'descricao' => 'Ver lista de funcionários'],
 ['nome' => 'Criar Funcionários', 'slug' => 'funcionarios.create', 'modulo' => 'RH', 'descricao' => 'Cadastrar funcionários'],
 ['nome' => 'Editar Funcionários', 'slug' => 'funcionarios.edit', 'modulo' => 'RH', 'descricao' => 'Editar funcionários'],
 ['nome' => 'Excluir Funcionários', 'slug' => 'funcionarios.delete', 'modulo' => 'RH', 'descricao' => 'Excluir funcionários'],
 ['nome' => 'Visualizar Cargos', 'slug' => 'cargos.view', 'modulo' => 'RH', 'descricao' => 'Ver cargos'],
 ['nome' => 'Gerenciar Cargos', 'slug' => 'cargos.manage', 'modulo' => 'RH', 'descricao' => 'Criar, editar e excluir cargos'],
 ['nome' => 'Visualizar Comissões', 'slug' => 'comissoes.view', 'modulo' => 'RH', 'descricao' => 'Ver comissões'],
 ['nome' => 'Gerenciar Comissões', 'slug' => 'comissoes.manage', 'modulo' => 'RH', 'descricao' => 'Calcular e pagar comissões'],
 ['nome' => 'Visualizar Bônus', 'slug' => 'bonus.view', 'modulo' => 'RH', 'descricao' => 'Ver bônus'],
 ['nome' => 'Gerenciar Bônus', 'slug' => 'bonus.manage', 'modulo' => 'RH', 'descricao' => 'Criar e pagar bônus'],
 ['nome' => 'Visualizar Financeiro', 'slug' => 'financeiro.view', 'modulo' => 'Financeiro', 'descricao' => 'Ver módulo financeiro'],
 ['nome' => 'Gerenciar Faturas', 'slug' => 'financeiro.faturas', 'modulo' => 'Financeiro', 'descricao' => 'Criar e gerenciar faturas'],
 ['nome' => 'Relatórios Financeiros', 'slug' => 'financeiro.reports', 'modulo' => 'Financeiro', 'descricao' => 'Ver relatórios financeiros'],
 ['nome' => 'Configurações Empresa', 'slug' => 'config.empresa', 'modulo' => 'Configurações', 'descricao' => 'Configurar dados da empresa'],
 ['nome' => 'Gerenciar Usuários', 'slug' => 'usuarios.manage', 'modulo' => 'Configurações', 'descricao' => 'Criar e gerenciar usuários'],
 ['nome' => 'Gerenciar Permissões', 'slug' => 'permissoes.manage', 'modulo' => 'Configurações', 'descricao' => 'Gerenciar permissões e cargos'],
 ];
 foreach ($permissoes as $permissao) {
 Permissao::firstOrCreate(
 ['slug' => $permissao['slug']],
 $permissao
 );
 }
 $this->command->info('Permissões criadas com sucesso!');
 }
}