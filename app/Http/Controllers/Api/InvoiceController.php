<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    // GET /api/invoices
    public function index(Request $request)
    {
        $invoices = $request->user()
            ->invoices()
            ->with('items.feeType')
            ->latest()
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $invoices
        ]);
    }
}
