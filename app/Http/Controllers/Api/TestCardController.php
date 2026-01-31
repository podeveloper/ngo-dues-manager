<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TestCard;
use Illuminate\Http\Request;

class TestCardController extends Controller
{
    public function index()
    {
        $cards = TestCard::orderBy('should_succeed', 'desc')
            ->orderBy('bank_name')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $cards
        ]);
    }
}
