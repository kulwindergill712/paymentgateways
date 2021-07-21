<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentGatewayTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_gateway_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('gateway_id');
            $table->string('customer_id');
            $table->string('gateway_identifier');
            $table->string('amount');
            $table->string('payment_link')->default("");
            $table->string('status')->default(0);
            $table->string('transaction_id')->default("");

            $table->timestamps();
        });
    }
    protected $table = 'payment_gateway_transactions';
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_gateway_transactions');
    }
}
