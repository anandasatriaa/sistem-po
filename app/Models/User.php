<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    public $incrementing = false;

    protected $table = 'users';
    
    protected $primaryKey = 'ID';
    
    protected $fillable = [
        'ID',
        'Nama',
        'JK',
        'TmpLahir',
        'TglLahir',
        'Agama',
        'Pendidikan',
        'Alamat',
        'Alamat_dom',
        'Kota',
        'KodePos',
        'Telpon',
        'KTP',
        'Status',
        'JA',
        'TglMasuk',
        'TglLulus',
        'TglUpdate',
        'Aktif',
        'TglKeluar',
        'Jabatan',
        'Divisi',
        'Dept',
        'Cabang',
        'Golongan',
        'jeniskar',
        'statuskar',
        'no_bpjs_tk',
        'no_bpjs_kes',
        'tgl_keper_bpjs',
        'statBpjs',
        'Atasan',
        'JamKerja',
        'total_telat',
        'NoSuratKerja',
        'NoSuratKerja2',
        'MasaBerlaku',
        'MasaBerlaku2',
        'TjMakan',
        'stat_makan',
        'NilaiTjMakan',
        'TjBBM',
        'NilaiTjBBM',
        'stat_BBM',
        'TjAsuransi',
        'TjAssEff',
        'TjAssPolis',
        'TjPengobatan',
        'TjKerajinan',
        'TjLembur',
        'SaldoTjPengobatan',
        'TjUmObMinggu',
        'IDMesin',
        'StatusNoPrick',
        'Pajak',
        'Npwp',
        'hak_cuti',
        'jml_cuti',
        'jml_off',
        'nokk',
        'email_karyawan',
        'email_atasan',
        'uname',
        'pwd',
        'lvl',
        'abs',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'pwd', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getAuthIdentifierName()
    {
        return 'uname';
    }

    public function getAuthPassword()
    {
        return $this->pwd;
    }
}
