<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Locations extends Model
{
    use HasFactory;
    protected $table="pricing";


    protected $guarded=[];

    public static function getprice(Locations $loc1,Locations $loc2,$count)
    {
        $price=Settings::first()->price;
        $distance=$loc1->distance>$loc2->distance?$loc1->distance-$loc2->distance:$loc2->distance-$loc1->distance;
        $rate=$distance*$price;
        return $count==1?$rate:$rate*1.5;
    }
}
