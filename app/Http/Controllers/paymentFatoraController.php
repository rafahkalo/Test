<?php

namespace App\Http\Controllers;

use App\Models\ExternalReservation;
use App\Models\PaymentFatora;
use App\Models\Payment_Type;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class paymentFatoraController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $paymentFatoras = PaymentFatora::all(); // Retrieve all payment fatora records

        return response()->json(['data' => $paymentFatoras], 200); // Return the records as JSON response

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'is_payment' => 'required|boolean',
            'payment_id' => 'required',
            'user_id' => 'required',
            'paymentAmount' => 'required|numeric',
        ]);

        $paymentFatora = PaymentFatora::create([
            'is_payment' => $request->input('is_payment'),
            'payment_id' => $request->input('payment_id'),
            'user_id' => $request->input('user_id'),
            'paymentAmount' => $request->input('paymentAmount'),
        ]);

        return response()->json(['message' => 'Payment Fatora created successfully', 'data' => $paymentFatora], 201);

    }

    public function paymentMethod($Reservation,$typePayment){
        $Reservation= ExternalReservation::where('id',$Reservation)->get();
        if(!$Reservation)
        return response()->json(['message' => 'you do not have any reservation']);
        $type=Payment_Type::wherw('name',$typePayment)->get();
    if($type->name =='cash')
    {
        $paymentFatora = PaymentFatora::create([
            'payment_id' => $type->id,
            'user_id' => Auth::user(),
            'paymentAmount' =>  $Reservation->cost,
        ]);
        $Reservation->update('paymentfatora_id',$paymentFatora->id);
        return response()->json(['message' => 'Payment cash successfully', ' Payment information' => $paymentFatora ], 201);

    }
    elseif($type->name =='electronic')
    {
        $paymentFatora = PaymentFatora::create([
            'payment_id' => $type->id,
            'user_id' => Auth::user(),
            'is_payment'=> 1,
            'paymentAmount' =>  $Reservation->cost,
        ]);
        $Reservation->update('paymentfatora_id',$paymentFatora->id);
        return response()->json(['message' => 'Payment electronic successfully', ' Payment information' => $paymentFatora ], 201);
    }




    }

}