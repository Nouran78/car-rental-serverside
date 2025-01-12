<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable =
    ['user_id',
     'car_id',
     'start_date',
      'end_date',
      'total_price',
    'payment_status',
];
public function user()
{
    return $this->belongsTo(User::class);
}

public function car()
{
    return $this->belongsTo(Car::class);
}

}
