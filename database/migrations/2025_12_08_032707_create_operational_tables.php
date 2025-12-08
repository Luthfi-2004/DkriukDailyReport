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
    // 1. Tabel Master Item (Mainan Super Admin)
    Schema::create('master_items', function (Blueprint $table) {
        $table->id();
        $table->string('name'); // Contoh: 'Ayam Potong'
        $table->string('unit'); // Contoh: 'Ekor', 'Kg', 'Liter'
        $table->decimal('price', 15, 2)->default(0); // [BARU] Harga per satuan. Pakai decimal biar presisi.
        $table->boolean('is_active')->default(true);
        $table->timestamps();
    });

    // 2. Tabel Header Laporan (Amplop Laporan User)
    Schema::create('daily_reports', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained('users'); // Siapa yang lapor
        $table->date('report_date'); // Kapan
        $table->enum('status', ['draft', 'submitted'])->default('draft'); // Status kunci
        $table->text('notes')->nullable(); // Catatan tambahan user
        $table->timestamps();

        // Mencegah user lapor 2x di tanggal yang sama (Opsional tapi disarankan)
        $table->unique(['user_id', 'report_date']);
    });

    // 3. Tabel Detail Angka (Isi Laporan)
    Schema::create('daily_report_details', function (Blueprint $table) {
        $table->id();
        $table->foreignId('daily_report_id')->constrained('daily_reports')->onDelete('cascade');
        $table->foreignId('master_item_id')->constrained('master_items'); // Barang apa
        $table->decimal('quantity', 10, 2); // Jumlahnya (bisa koma)
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operational_tables');
    }
};
