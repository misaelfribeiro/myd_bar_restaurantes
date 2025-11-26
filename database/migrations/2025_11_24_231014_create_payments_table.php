<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pedido_id');
            $table->string('numero_pedido')->nullable();
            $table->string('tenant_code')->nullable();
            $table->string('mp_payment_id')->unique()->nullable(); // ID do pagamento no Mercado Pago
            $table->string('mp_preference_id')->nullable(); // ID da preferência criada
            $table->string('payment_method')->default('pix'); // pix, credit_card, debit_card, boleto
            $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled', 'refunded', 'in_process'])->default('pending');
            $table->decimal('amount', 10, 2);
            $table->decimal('platform_fee', 10, 2)->default(0); // Taxa da plataforma EATSFOOD
            $table->decimal('gateway_fee', 10, 2)->default(0); // Taxa do Mercado Pago
            $table->decimal('net_amount', 10, 2); // Valor líquido para o restaurante
            $table->text('pix_qr_code')->nullable(); // QR Code em base64
            $table->text('pix_qr_code_url')->nullable(); // URL da imagem do QR Code
            $table->text('pix_copy_paste')->nullable(); // Código Pix Copia e Cola
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->text('mp_response')->nullable(); // JSON da resposta completa do MP
            $table->text('refund_reason')->nullable();
            $table->timestamps();
            
            $table->foreign('pedido_id')->references('id')->on('pedidos')->onDelete('cascade');
            $table->index('mp_payment_id');
            $table->index('status');
            $table->index('tenant_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
}
