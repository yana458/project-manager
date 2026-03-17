<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();

            $table->foreignId('client_id')
                ->constrained('clients')
                ->cascadeOnDelete();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('name');
            $table->text('description')->nullable();
            $table->string('status')->default('active'); // active | paused | finished
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            $table->string('repo_url')->nullable();
            $table->string('staging_url')->nullable();
            $table->string('production_url')->nullable();
            $table->string('docs_url')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};