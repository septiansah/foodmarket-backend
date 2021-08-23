<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Midtrans\Config;
use Midtrans\Snap;

class TransactionController extends Controller
{
    //
    public function all(Request $request)
    {
        $id = $request->input('id');
        $limit = $request->input('limit');
        $food_id = $request->input('food_id');
        $status = $request->input('status');


        if($id){
            $transaction = Transaction::with(['food', 'user'])->find($id);
            if($transaction)
            {
                return ResponseFormatter::success([
                    $transaction,
                    'Data transaksi berhasil diambil'
                ]);
            }
            else
            {
                return ResponseFormatter::error([
                    null,
                    'Data transaksi tidak ada',
                    404
                ]);
            }
        }

        $transaction = Transaction::with(['food', 'user'])
                        ->where('user_id', Auth::user()->id);

        if($food_id)
        {
            $transaction->where('name', $food_id);
        }

        if($status)
        {
            $transaction->where('status'. $status);
        }


        return ResponseFormatter::success(
            $transaction->paginate($limit),
            'Date list transaksi berhasil diambil'
        );

    }

    public function checkout(Request $request)
    {
        $request->validate([
            'food_id' => 'required|exists:food,id',
            'user_id' => 'required|exists:users,id',
            'quantity' => 'required',
            'total' => 'required',
            'status' => 'required',
        ]);

        $transaction = Transaction::create([
            'food_id' => $request->food_id,
            'user_id' => $request->user_id,
            'quantity' => $request->quantity,
            'total' => $request->total,
            'status' => $request->status,
            'payment_url' => '',
        ]);

        // Konfigurasi Midtrans
        Config::$serverKey = config('services.midtrans.serverKey');
        Config::$isProduction = config('services.midtrans.isProduction');
        Config::$isSanitized = config('services.midtrans.isSenitized');
        Config::$is3ds = config('services.midtrans.is3ds');

        // Panggil transaksi yang telah dibuat
        $transaction = Transaction::with(['food', 'user'])->find($transaction->id);

        // panggil transaksi midtrans

        $midtrans = [
            'transaction_detail' => [
                'order_id' => $transaction->id,
                'gross_amount' => (int) $transaction->total
            ],
            'customer_detail' => [
                'first_name' => $transaction->user->name,
                'email' => $transaction->user->email,
            ],
            'enable_payment' => ['gopay, bank_transfer'],
            'vtweb' => []
        ];

        // memangfgil midtrans

        try{
            $paymentUrl = Snap::createTransaction($midtrans)->redirectUrl;
            $transaction->payment_url = $paymentUrl;
            $transaction->save();

            return ResponseFormatter::success([$transaction, 'Transaksi berhasil']);
        }catch(Exception $e)
        {
            return ResponseFormatter::error($e->getMessage(), 'Transaksi gagal');
        }
    }
}
