<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBranchIdToOrderGroupsTable extends Migration
{
    public function up()
    {
        Schema::table('order_groups', function (Blueprint $table) {
            $table->unsignedBigInteger('branch_id')->nullable()->after('id'); // Add branch_id column
            // Add foreign key constraint if you have a branches table
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('order_groups', function (Blueprint $table) {
            $table->dropForeign(['branch_id']); // Drop foreign key if exists
            $table->dropColumn('branch_id'); // Drop the column
        });
    }
}
