<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyAdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ads', function (Blueprint $table) {
            $table->unsignedBigInteger('payment')->after('phone')->nullable();
            $table->set('gender', ['male', 'female', 'both'])->after('payment');
            $table->boolean('service')->after('gender')->default(0);
            $table->set('type', ['full-time', 'part-time'])->after('service');
            $table->text('address')->after('info');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ads', function (Blueprint $table) {
            $table->dropColumn(['payment', 'gender', 'service', 'type', 'address']);
        });
    }
}
