<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('promos', function (Blueprint $table) {
            $table->id();
            $table->string('code', 32)->unique();
            $table->tinyInteger('discount_percent')->unsigned()->nullable();
            $table->smallInteger('discount_amount')->unsigned()->nullable();
            $table->dateTime('valid_till');
            $table->integer('usage_limit')->nullable();
            $table->smallInteger('per_usage_limit')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promos');
    }
};
