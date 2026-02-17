<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('tb_audit_kantin')) {
            Schema::create('tb_audit_kantin', function (Blueprint $table): void {
                $table->bigIncrements('id');
                $table->bigInteger('order_id')->nullable()->index();
                $table->string('action', 60)->index();
                $table->text('description')->nullable();
                $table->integer('actor_id')->nullable()->index();
                $table->string('actor_username', 100)->nullable();
                $table->timestamp('created_at')->useCurrent();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_audit_kantin');
    }
};
