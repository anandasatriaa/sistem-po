<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseOrderMapTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_order_map', function (Blueprint $table) {
            $table->id();
            $table->string('no_po')->unique();
            $table->string('cabang_id');
            $table->string('cabang'); // Denormalized nama cabang
            $table->unsignedBigInteger('supplier_id');
            $table->string('supplier'); // Denormalized nama supplier
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('fax')->nullable();
            $table->string('up')->nullable();
            $table->date('date');
            $table->date('estimate_date');
            $table->text('remarks')->nullable();
            $table->string('sub_total');
            $table->string('pajak')->nullable();
            $table->string('discount')->nullable();
            $table->string('total');
            $table->string('ttd_1')->nullable();
            $table->string('ttd_2')->nullable();
            $table->string('ttd_3')->nullable();
            $table->string('nama_1')->nullable();
            $table->string('nama_2')->nullable();
            $table->string('nama_3')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('cabang_id')->references('id_cabang')->on('cabang')->onDelete('cascade');
            $table->foreign('supplier_id')->references('id')->on('supplier')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_order_map');
    }
}
