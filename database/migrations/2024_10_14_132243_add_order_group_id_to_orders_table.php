<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOrderGroupIdToOrdersTable extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('order_group_id')->nullable()->after('id');
            $table->foreign('order_group_id')->references('id')->on('order_groups')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['order_group_id']);
            $table->dropColumn('order_group_id');
        });
    }
}
