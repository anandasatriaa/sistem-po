<?php

namespace App\Models\PR;

use App\Models\User;
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
        'status',
        'acc_sign',
        'acc_by'
    ];

    public function barang()
    {
        return $this->hasMany(PurchaseRequestBarang::class, 'purchase_request_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function lampiran()
    {
        return $this->hasMany(PurchaseRequestLampiran::class);
    }
}
