<?php

namespace App\Models\PO;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderBarangMilenia extends Model
{
    use HasFactory;

    // Nama tabel yang terkait dengan model ini
    protected $table = 'purchase_order_barang_milenia';

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

    // Relasi ke model PurchaseOrderMilenia
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrderMilenia::class, 'purchase_order_id');
    }
}
