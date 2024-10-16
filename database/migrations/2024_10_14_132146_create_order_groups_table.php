<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_groups', function (Blueprint $table) {
            $table->id();
            $table->string('payment_method')->nullable();
            $table->string('e_receipt')->nullable();
            $table->string('status')->default('Pending');
            $table->string('customer_name')->default('N/A');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_groups');
    }
}
