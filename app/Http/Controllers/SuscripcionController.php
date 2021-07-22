<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SuscripcionController extends Controller
{
    public function pago(Request $request)
    {
        dd($request->all());
    }
}
