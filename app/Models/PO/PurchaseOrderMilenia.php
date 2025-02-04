<?php

namespace App\Models\PO;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderMilenia extends Model
{
    use HasFactory;

    protected $table = 'purchase_order_milenia';

    protected $fillable = [
        'no_po',
        'cabang_id',
        'cabang',
        'supplier_id',
        'supplier',
        'address',
        'phone',
        'fax',
        'up',
        'date',
        'estimate_date',
        'remarks',
        'sub_total',
        'pajak',
        'discount',
        'total',
        'ttd_1',
        'ttd_2',
        'ttd_3',
        'nama_1',
        'nama_2',
        'nama_3',
        'status',
    ];

    public function barang()
    {
        return $this->hasMany(PurchaseOrderBarangMilenia::class, 'purchase_order_id');
    }
}