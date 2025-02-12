<?php

namespace App\Models\PR;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseRequestLampiran extends Model
{
    use HasFactory;

    protected $table = 'purchase_request_lampiran';

    protected $fillable = [
        'purchase_request_id',
        'file_path',
    ];
}
