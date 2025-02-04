<?php

namespace App\Models\PO;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderBarangMAP extends Model
{
    use HasFactory;

    // Nama tabel yang terkait dengan model ini
    protected $table = 'purchase_order_barang_map';

    // Kolom yang dapat diisi (mass assignable)
    protected $fillable = [
        'purchase_order_id',
        'category_id',
        'category',
        'barang_id',
        'barang',
        'qty',
        'unit_id',
        'unit',
        'keterangan',
        'unit_price',
        'amount_price',
    ];

    // Relasi ke model PurchaseOrderMAP
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrderMAP::class, 'purchase_order_id');
    }
}
