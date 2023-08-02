<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InformasiModel extends Model
{
    use SoftDeletes;
    //
    protected $table = 'informasi';

    protected $fillable = [ 'url', 'title', 'desc_informasi' ];
}
