<?php

namespace App\Http\Controllers;

use App\Models\Debt;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DebtController extends Controller
{
    public function index()
    {
        $debts = Debt::all();
        if ($debts->count() > 0) {
            return response()->json([
                'status' => 200,
                'debts' => $debts
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'No records found!'
            ], 404);
        }
    }

    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'lender_name' => 'required|string|max:255',
            'amount' => 'required|int',
            'return_date' => 'required|date'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validate->messages()
            ], 422);
        } else {
            $debts = Debt::create([
                'lender_name' => $request->lender_name,
                'amount' => $request->amount,
                'return_date' => $request->return_date
            ]);
            if ($debts) {
                return response()->json([
                    'status' => 200,
                    'message' => 'Debt created!'
                ], 200);
            } else {
                return response()->json([
                    'status' => 500,
                    'message' => 'Something went wrong!'
                ], 500);
            }
        }
    }

    public function expiredDebts()
    {
        $debts = Debt::select('lender_name', 'created_at as lending_date', 'return_date')
            ->where('return_date', '<', Carbon::now())->get();

        if ($debts->count() > 0) {
            return response()->json([
                'status' => 200,
                'debts' => $debts
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'No expired debts found!'
            ], 404);
        }
    }

}
