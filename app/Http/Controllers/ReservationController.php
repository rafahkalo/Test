<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
class ReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [

            'driver_id' => 'required',
            'date' =>'required',
            'travel_time' => 'required',
            'goal' => 'required',
            'location' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        Reservation::create([
                'goal'=>$request->goal,
                'location'=>$request->location,
                'driver_id'=>$request->driver_id,
                'user_id'=>Auth::user()->id,
             
                'date'=>$request->date,
                'travel_time'=>$request->travel_time
        ]);



        return response()->json(["message"=>"Ok", 201]);

    }
    public function acceptReservation($id){

        $reservation=Reservation::find($id);
        if($reservation){
        Reservation::where('id',$id)->update(['status'=>true]);

        return response()->json(['message' => "Accept this order" , 200]);
        }
else{
    return response()->json(['message'=>"There Are wrong in this order" , 200]);

}



    }


    /**
     * Display the specified resource.
     */

     public function show($id)
     {
         $input=Reservation::find($id);
         return response()->json(['message'=>$input], 200);
     }
 
     /**
      * Show the form for editing the specified resource.
      */
     public function startTrip($id)
     {
         $reservation=Reservation::find($id);
 $status=$reservation->status;
 
 
         if($status==1){
         Reservation::where('id',$id)->update(['finish'=>true]);
 
         return response()->json(['message' => "Start this trip" , 200]);
         }
 else{
     return response()->json(['message'=>"This Trip is not accepted" , 200]);
 
 }
     }
 
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Reservation $reservation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Reservation $reservation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function searchByTime(Request $request)
    {
       $reservation= Reservation::where('travel_time',$request->time)->get();

       return response()->json(['data' => $reservation , 200]);
       
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {

        $reservation=Reservation::find($id);
        $reservation->delete();
        return response()->json(["message"=>"Deleted successfuly", 200]);



    }
}
