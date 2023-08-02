<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class HalteModel extends Model
{
    //
    use SoftDeletes;
    //
    protected $table = 'halte';

    protected $fillable = ['id', 'halte_name', 'date', 'arrival_time_in_halte', 'departure_time_in_halte'];

    protected $primaryKey = 'id';

    public $incrementing = false; // Mengatur agar primary key tidak berinkrementasi (jika menggunakan primary key berupa string)

    public static function boot()
    {
        parent::boot();

        // Membuat UUID saat menyimpan model
        static::creating(function ($model) {
            $model->id = Str::uuid();
        });
    }
    //
    public function koridor()
    {
        return $this->belongsTo(KoridorModel::class, 'koridor_id', 'id');
    }

    public function halte_schedule()
    {
        return $this->hasMany(HalteScheduleModel::class, 'halte_id', 'id');
    }
}