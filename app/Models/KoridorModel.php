<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class KoridorModel extends Model
{
    //
    use SoftDeletes;
    //
    protected $table = 'koridors';

    protected $fillable = ['id', 'koridor_name'];

    protected $primaryKey = 'id'; // Primary key adalah kolom 'id' yang auto increment

    public $incrementing = false; // Mengatur agar primary key tidak berinkrementasi (jika menggunakan primary key berupa string)

    public static function boot()
    {
        parent::boot();

        // Membuat UUID saat menyimpan model
        static::creating(function ($model) {
            $model->id = Str::uuid();
        });
    }

    public function halte()
    {
        return $this->hasMany(HalteModel::class, 'koridor_id', 'id');
    }
}