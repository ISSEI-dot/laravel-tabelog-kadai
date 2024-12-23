<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Overtrue\LaravelFavorite\Traits\Favoriteable;
use Kyslik\ColumnSortable\Sortable;

class Product extends Model
{
    use HasFactory,Favoriteable,Sortable;

    protected $fillable = [
        'name',
        'description',
        'price',
        'price_max', 
        'regular_holiday', 
        'opening_time', 
        'closing_time',
        'category_id',
        'image',
        'recommend_flag',
        'carriage_flag',
    ];

    public function category()
     {
         return $this->belongsTo(Category::class);
     }

     public function reviews()
     {
         return $this->hasMany(Review::class);
     }

     public function reservations()
    {
    return $this->hasMany(Reservation::class);
    }

}
