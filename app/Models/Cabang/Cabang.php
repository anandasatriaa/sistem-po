<?php

namespace App\Models\Cabang;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cabang extends Model
{
    use HasFactory;

    protected $table = 'cabang';
    protected $primaryKey = 'id_cabang';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_cabang',
        'nama',
        'alamat',
        'kota',
        'provinsi',
        'telepon',
        'pic',
        'aktif',
    ];
}
