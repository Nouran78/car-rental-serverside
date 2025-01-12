<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    use HasFactory;
    protected $fillable =
    ['name',
    'type',
    'price_per_day',
     'availability_status'
    ];
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

}
