<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAiLearningSystemTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Tabela de neurônios (camadas da rede neural)
        Schema::create('ai_neurons', function (Blueprint $table) {
            $table->id();
            $table->string('layer'); // input, hidden, output
            $table->integer('position'); // posição na camada
            $table->float('bias')->default(0); // viés do neurônio
            $table->float('activation')->default(0); // última ativação
            $table->string('type')->default('relu'); // relu, sigmoid, tanh
            $table->timestamps();
            
            $table->index(['layer', 'position']);
        });

        // Tabela de sinapses (conexões entre neurônios com pesos)
        Schema::create('ai_synapses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('from_neuron_id');
            $table->unsignedBigInteger('to_neuron_id');
            $table->float('weight')->default(0); // peso da sinapse
            $table->float('delta')->default(0); // ajuste acumulado
            $table->integer('updates')->default(0); // quantas vezes foi atualizado
            $table->timestamps();
            
            $table->foreign('from_neuron_id')->references('id')->on('ai_neurons')->onDelete('cascade');
            $table->foreign('to_neuron_id')->references('id')->on('ai_neurons')->onDelete('cascade');
            $table->index(['from_neuron_id', 'to_neuron_id']);
        });

        // Tabela de dados de treinamento (histórico de interações)
        Schema::create('ai_training_data', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->text('input'); // entrada do usuário
            $table->text('expected_output'); // saída esperada
            $table->text('actual_output')->nullable(); // saída que a IA deu
            $table->string('intent')->nullable(); // intenção detectada
            $table->json('context')->nullable(); // contexto da conversa
            $table->float('confidence')->default(0); // confiança da resposta
            $table->boolean('correct')->default(true); // se a resposta foi correta
            $table->integer('feedback_score')->nullable(); // 1-5 (ruim a excelente)
            $table->boolean('used_for_training')->default(false);
            $table->timestamp('trained_at')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->index(['intent', 'correct']);
            $table->index('used_for_training');
        });

        // Tabela de contextos aprendidos (conhecimento do sistema)
        Schema::create('ai_contexts', function (Blueprint $table) {
            $table->id();
            $table->string('category'); // menu, orders, delivery, products, etc
            $table->string('key'); // identificador único do contexto
            $table->text('pattern'); // padrão de entrada que ativa
            $table->text('response_template'); // template de resposta
            $table->json('parameters')->nullable(); // parâmetros extraídos
            $table->string('action')->nullable(); // ação a executar
            $table->integer('usage_count')->default(0); // quantas vezes foi usado
            $table->float('success_rate')->default(0); // taxa de sucesso
            $table->float('confidence_threshold')->default(0.6); // limiar de confiança
            $table->boolean('active')->default(true);
            $table->timestamps();
            
            $table->index(['category', 'active']);
            $table->index('key');
        });

        // Tabela de sessões de conversa (mantém contexto)
        Schema::create('ai_conversation_sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('session_token')->unique();
            $table->json('context_stack')->nullable(); // pilha de contextos
            $table->json('entities')->nullable(); // entidades extraídas
            $table->string('last_intent')->nullable();
            $table->integer('message_count')->default(0);
            $table->timestamp('last_activity')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index('session_token');
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ai_conversation_sessions');
        Schema::dropIfExists('ai_contexts');
        Schema::dropIfExists('ai_training_data');
        Schema::dropIfExists('ai_synapses');
        Schema::dropIfExists('ai_neurons');
    }
}
