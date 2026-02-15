<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('user')) {
            Schema::create('user', function (Blueprint $table): void {
                $table->integer('id', true);
                $table->string('username', 50);
                $table->string('password', 100);
                $table->integer('level');
                $table->string('Kios', 50);
            });
        }

        if (!Schema::hasTable('tb_kios')) {
            Schema::create('tb_kios', function (Blueprint $table): void {
                $table->integer('id', true);
                $table->string('nama', 50);
            });
        }

        if (!Schema::hasTable('tb_kategori_menu')) {
            Schema::create('tb_kategori_menu', function (Blueprint $table): void {
                $table->integer('id_kategori', true);
                $table->integer('jenis_menu');
                $table->string('kategori_menu', 500);
            });
        }

        if (!Schema::hasTable('tb_menu')) {
            Schema::create('tb_menu', function (Blueprint $table): void {
                $table->integer('id', true);
                $table->string('foto', 100);
                $table->string('nama', 100);
                $table->string('keterangan', 500);
                $table->integer('kategori');
                $table->string('nama_toko', 100);
                $table->double('harga');
                $table->double('pajak');
            });
        }

        if (!Schema::hasTable('tb_order')) {
            Schema::create('tb_order', function (Blueprint $table): void {
                $table->bigInteger('id_order')->primary();
                $table->string('pelanggan', 200);
                $table->string('meja');
                $table->integer('kasir')->index();
                $table->string('nama_kios', 200);
                $table->timestamp('waktu_order')->useCurrent();
                $table->string('catatan', 200);

                $table->foreign('kasir')->references('id')->on('user');
            });
        }

        if (!Schema::hasTable('tb_list_order')) {
            Schema::create('tb_list_order', function (Blueprint $table): void {
                $table->integer('id_list_order', true);
                $table->integer('menu')->index();
                $table->bigInteger('kode_order')->index();
                $table->integer('jumlah');
                $table->string('catatan_order')->nullable();
                $table->string('status')->nullable();

                $table->foreign('menu')->references('id')->on('tb_menu')->cascadeOnUpdate();
                $table->foreign('kode_order')->references('id_order')->on('tb_order')->cascadeOnUpdate();
            });
        }

        if (!Schema::hasTable('tb_bayar')) {
            Schema::create('tb_bayar', function (Blueprint $table): void {
                $table->bigInteger('id_bayar')->primary();
                $table->double('nominal_uang');
                $table->double('jumlah_bayar');
                $table->double('ppn');
                $table->double('nominal_toko');
                $table->double('nominal_rs');
                $table->double('diskon');
                $table->timestamp('waktu_bayar')->useCurrent();
                $table->bigInteger('kode_order_bayar');

                $table->foreign('id_bayar')->references('id_order')->on('tb_order')->cascadeOnUpdate();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_bayar');
        Schema::dropIfExists('tb_list_order');
        Schema::dropIfExists('tb_order');
        Schema::dropIfExists('tb_menu');
        Schema::dropIfExists('tb_kategori_menu');
        Schema::dropIfExists('tb_kios');
        Schema::dropIfExists('user');
    }
};
