<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class HalteScheduleModel extends Model
{
    //
    use SoftDeletes;

    protected $table = 'halte_schedule';

    protected $fillable = ['id', 'bus_queue', 'bus_name', 'arrival_time_bus', 'departure_time_bus'];

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

    public function halte()
    {
        return $this->belongsTo(HalteModel::class, 'halte_id', 'id');
    }

}