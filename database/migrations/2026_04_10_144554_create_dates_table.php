<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('dates', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->foreignId('patient_id')
                ->constrained('patients')
                ->onDelete('cascade');

            $table->foreignId('worker_id')
                ->constrained('workers')
                ->onDelete('cascade');

            $table->foreignId('test_id')
                ->constrained('test_types')
                ->onDelete('cascade');

            $table->date('date_time');

            $table->integer('time');

            $table->enum('estat', ['programada', 'cancel·lada', 'realitzada']);

            $table->enum('urgencia', ['no urgent', 'preferent', 'urgent']);

            $table->unique('date_time', 'worker_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dates');
    }
};
