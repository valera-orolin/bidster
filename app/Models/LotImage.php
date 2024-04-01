<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LotImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'image_path',
        'lot_id'
    ];

    public function lot()
    {
        return $this->belongsTo(Lot::class);
    }
}
