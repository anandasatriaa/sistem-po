<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseOrderBarangMileniaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_order_barang_milenia', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained('purchase_order_milenia')->onDelete('cascade');
            $table->unsignedBigInteger('category_id');
            $table->string('category'); // Denormalized nama kategori
            $table->unsignedBigInteger('barang_id');
            $table->string('barang'); // Denormalized nama barang
            $table->string('qty');
            $table->unsignedBigInteger('unit_id');
            $table->string('unit'); // Denormalized satuan unit
            $table->text('keterangan')->nullable();
            $table->string('unit_price');
            $table->string('amount_price');
            $table->timestamps();

            // Foreign keys
            $table->foreign('category_id')->references('id')->on('category');
            $table->foreign('barang_id')->references('id')->on('barang');
            $table->foreign('unit_id')->references('id')->on('unit');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_order_barang_milenia');
    }
}
