<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tricycle;
use App\Models\User;
use App\Models\Transactions;
use Illuminate\Support\Facades\Hash;

class TricycleController extends Controller
{
    public function index()
    {
        return \response(['drivers'=>Tricycle::all()]);
    }
    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'plate_no' => 'required|string|max:255',
            'cpnum' => 'required|string|max:255',
            
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);
        
        $user = User::create([
            'username' => $validatedData['username'],
            'cpnum' => $validatedData['cpnum'],
            'password' => Hash::make($validatedData['password']),
            'acctype' => 'driver',
            'status' => 'verified',
        ]);
        
        if($user){
            $tri=new Tricycle([
                'user_id'=>$user->id,
                'name'=>$request->input('name'),
                'license'=>$request->input('license'),
                'plate_no'=>$request->input('plate_no'),
                'cpnum'=>$request->input('cpnum'),
            ]);
            $tri->save();
            return \response(['status'=>'success','message'=>'Driver Added']);
        }
        // Tricycle::create(
        //     $request->all()
        // );
        return \response(['msg'=>'Driver added successfully','status'=>'success']);
    }
    public function destroy(Tricycle $id)
    {
        $id->delete();
        return \response(['msg'=>'Driver deleted successfully','status'=>'success']);
    }
    public function show(Tricycle $id)
    {
        $count=$id->transactions()->where('status','done')->count();
        $rate=$count>0?$id->transactions()->where('status','done')->sum('rating')/$count:0; 
        $comments= $id->transactions()->orderBy('id','desc')->where('status','done')->take(5)->get()->pluck('comment')->toArray();
        return \response([
            'driver'=>$id,
            'status'=>'success',
            'rate'=>[
                'rate'=>$rate,
                'count'=>$count
            ],
            'comments'=>$comments
        ]);
    }
}
