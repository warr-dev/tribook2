<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transactions extends Model
{
    use HasFactory;
    protected $table='service';

    protected $guarded=[];
    protected $hidden=['code'];

    public function client()
    {
        return $this->belongsTo(User::class,'user_id');
    }
    public function drivers()
    {
        return $this->belongsToMany(Tricycle::class,'driver_service','transaction_id','driver_id');
    }
    public function loc1()
    {
        return $this->belongsTo(Locations::class,'pickup');
    }
    public function loc2()
    {
        return $this->belongsTo(Locations::class,'destination');
    }
    protected $with=["drivers",'client','loc1','loc2'];
}
