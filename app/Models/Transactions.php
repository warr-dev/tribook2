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
    public static function getnextdriver($counter){
        $maxpass=Settings::first()->max_passenger;
        $count=$counter/$maxpass;

        if($counter%$maxpass!=0) $count++;
        // $lastid=Tricycle::where('status','active')->orderBy('id','desc')->first()->id;
        // $lasttrans=Transactions::orderBy('id','desc')->first()->id??0;
        // // if no transanction get first drivers
        // if($lasttrans==0)
        //     return Tricycle::where('status','active')->take($count)->get()->pluck('id')->toArray();
        // // get last transaction
        // $trans=Transactions::orderBy('service.id','desc')->join('driver_service','service.id','driver_service.transaction_id')->where('driver_service.transaction_id',$lasttrans)->get()->pluck('driver_id')->toArray();
        
        // if(!$trans)
        //     return Tricycle::where('status','active')->take($count)->get()->pluck('id')->toArray();
        // if(sizeof($trans)==0)
        //     return Tricycle::where('status','active')->take($count)->get()->pluck('id')->toArray();
        // else{
        //     // get driver id of last transaction
        //     $max=max($trans);
            
        //     // if last on line back to first
        //     if($lastid==$max)
        //         return Tricycle::where('status','active')->orderBy('id','asc')->take($count)->get()->pluck('id')->toArray();
        //     // get the next list of driver after the last transaction
        //     $ids=Tricycle::where('id','>',$max)->where('status','active')->orderBy('id','asc')->take($count)->get()->pluck('id')->toArray();
        //     $ctr=sizeof($ids);
        //     if($ctr<$count){
        //         $ids=array_merge($ids,Tricycle::where('status','active')->orderBy('id','asc')->take($count-$ctr)->get()->pluck('id')->toArray());
        //     }
        //     return $ids;
        // }
        $lasttrans=Transactions::orderBy('id','desc')->first()->id??0;
        $lastdriver=Transactions::select('tricycle.passenger','tricycle.id')->orderBy('driver_service.id','desc')->join('driver_service','service.id','driver_service.transaction_id')->join('tricycle','tricycle.id','driver_service.driver_id')->where('driver_service.transaction_id',$lasttrans)->first();
        if(!$lastdriver)
            return Tricycle::where('status','active')->take($count)->get()->pluck('id')->toArray();
        $operation='>';
        if($lastdriver->passenger!=$maxpass){
            $counter+=$lastdriver->passenger;
            $operation='>=';
        }
        $count=$counter/$maxpass;
        $hasExtra=false;
        if($counter%$maxpass!=0){
            $count++;
            $hasExtra=true;
        }
        $nextdrivers=Tricycle::where('id',$operation,$lastdriver->id)->where('status','active')->orderBy('id','asc')->take($count)->get()->pluck('id')->toArray();
        $ctr=sizeof($nextdrivers);
        if($ctr<$count){
            $nextdrivers=array_merge($nextdrivers,Tricycle::where('status','active')->orderBy('id','asc')->take($count-$ctr)->get()->pluck('id')->toArray());
        }
        $drivers=Tricycle::find($nextdrivers);
        $i=1;
        foreach($drivers as $driver){
            $driver->passenger=$maxpass;
            if($hasExtra && $i==sizeof($drivers)){
                $driver->passenger=$counter%$maxpass;
            }
            $driver->save();
            $i++;
        }
        
        return $nextdrivers;
    }
    
    protected $with=["drivers",'client','loc1','loc2'];
}
