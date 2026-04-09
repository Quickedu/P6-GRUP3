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
        if (! Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                // Existing users need a default role when adding a non-nullable column.
                $table->enum('role', ['admin', 'worker', 'user'])->default('user')->after('email');
            });
        }

        Schema::create('worker_roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });
        Schema::create('workers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('nss')->unique();
            $table->string('adress');
            $table->string('dni')->unique();
            $table->integer('phone');
            $table->string('password');
            $table->foreignId('worker_role_id')->constrained('worker_roles')->onDelete('cascade');
            $table->timestamps();
        });
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('password');
            $table->timestamps();
        });
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('nts')->unique();
            $table->string('adress');
            $table->string('dni')->unique();
            $table->integer('phone');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workers');
        Schema::dropIfExists('admins');
        Schema::dropIfExists('patients');

        Schema::dropIfExists('worker_roles');

        if (Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('role');
            });
        }

    }
};
