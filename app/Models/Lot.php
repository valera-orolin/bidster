<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Lot extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'address',
        'description',
        'end_date',
        'starting_price',
    ];

    public static function boot()
    {
        parent::boot();

        static::deleting(function ($lot) {
            foreach ($lot->images as $image) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $image->image_path));
                $image->delete();
            }
        });
    }

    public function images() 
    {
        return $this->hasMany(LotImage::class);
    }

    public function characteristics() 
    {
        return $this->hasMany(Characteristic::class);
    }

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class);
    }

    public function category()
    {
        return $this->subcategory->category;
    }
}
