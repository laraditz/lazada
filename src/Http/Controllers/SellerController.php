<?php

namespace Laraditz\Lazada\Http\Controllers;

use Illuminate\Http\Request;
use Laraditz\Lazada\Exceptions\LazadaException;
use Laraditz\Lazada\Facades\Lazada;

class SellerController extends Controller
{
    public function authorized(Request $request)
    {
        $code = $request->code;

        throw_if(!$code, LazadaException::class, __('Missing code.'));

        try {
            $accessToken = Lazada::auth()->accessToken(code: $code);

            $seller = Lazada::seller()->info();

            return view('lazada::sellers.authorized', [
                'code' => $code,
                'seller' => $seller,
            ]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
