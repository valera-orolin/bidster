<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Characteristic extends Model
{
    use HasFactory;

    protected $fillable = [
        'lot_id',
        'name',
        'value',
    ];

    public function lot()
    {
        return $this->belongsTo(Lot::class);
    }
}
