<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    use HasFactory;

    protected $fillable = [
        'nis',
        'nama',
        'rombel',
        'rayon',
        'password',
        'medsos',
        'portofolio',
        'certifikat'
    ];

    protected $hidden = [
        'password'
    ];
}