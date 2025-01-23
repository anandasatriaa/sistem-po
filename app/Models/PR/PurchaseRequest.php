<?php

namespace App\Models\PR;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseRequest extends Model
{
    use HasFactory;

    protected $table = 'purchase_request';

    protected $fillable = [
        'user_id',
        'date_request',
        'divisi',
        'no_pr',
        'pt',
        'important',
        'status'
    ];
}
