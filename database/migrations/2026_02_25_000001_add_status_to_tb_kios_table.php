<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('tb_kios') && !Schema::hasColumn('tb_kios', 'status')) {
            Schema::table('tb_kios', function (Blueprint $table): void {
                $table->tinyInteger('status')->default(1)->after('nama');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('tb_kios') && Schema::hasColumn('tb_kios', 'status')) {
            Schema::table('tb_kios', function (Blueprint $table): void {
                $table->dropColumn('status');
            });
        }
    }
};
