<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WalletController extends Controller
{    
    /**
     * Wallet Pay
     *
     * @param  mixed $request
     * @return void
     */
    public function Pay(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), 
            [
                'user_id' => 'required',
                'product' => 'required',
                'quantity' => 'required|numeric|gt:0',
            ]);


            if($validation->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validation->errors()
                ], 401);
            }

            $user = $request->user();
            $amount = floatval(round($request->quantity) * 1);

            if ($amount > $user->wallet) {
                return response()->json([
                    'status' => false,
                    'message' => 'Insufficient Balance in Wallet',
                    'errors' => 'Insufficient Balance'
                ], 401);
            }

            $user->wallet = $user->wallet - floatval($amount);
            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'Payment succesfully completed',
                'data' => ['balance' => floatval(number_format($user->wallet,2)) ]
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
        
    }
    
    /**
     * Add to Wallet
     *
     * @param  mixed $request
     * @return void
     */
    public function Add(Request $request)
    {
        try {

            $validation = Validator::make($request->all(), 
            [
                'user_id' => 'required',
                'amount' =>  'required|numeric|min:3|max:100'
            ]);

            if($validation->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validation->errors()
                ], 401);
            }

            $user = $request->user();
            $user->wallet = $user->wallet + floatval($request->amount);
            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'Amount is successfully Added to Wallet',
                'data' => ['balance' => floatval(number_format($user->wallet,2)) ]
            ]);
            
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
