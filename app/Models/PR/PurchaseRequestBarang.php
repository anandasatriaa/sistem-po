<?php

namespace App\Models\PR;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseRequestBarang extends Model
{
    use HasFactory;

    protected $table = 'purchase_request_barang';

    protected $fillable = [
        'purchase_request_id',
        'no_pr',
        'nama_barang',
        'quantity',
        'unit',
        'keterangan'
    ];
}
