<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('reward_points', 10, 2)->default(0)->nullable();
            $table->decimal('credits', 10, 2)->default(0)->nullable();
            $table->boolean('is_first_shopping')->default(false)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // $table->dropColumn('reward_points');
            // $table->dropColumn('credits');
            // $table->dropColumn('is_first_shopping');
        });
    }
}
