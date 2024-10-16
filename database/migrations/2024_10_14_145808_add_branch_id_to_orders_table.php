<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBranchIdToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('branch_id')->nullable()->after('id'); // Make it nullable if necessary
            // Add any foreign key constraint if needed
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['branch_id']); // If you added a foreign key
            $table->dropColumn('branch_id');
        });
    }

}
