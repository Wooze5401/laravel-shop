<?php

namespace App\Http\Controllers;

use App\Exceptions\CouponCodeUnavailableException;
use App\Models\CouponCode;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CouponCodesController extends Controller
{
    public function show($code, Request $request)
    {
        if (!$record = CouponCode::where('code', $code)->first()) {
            throw new CouponCodeUnavailableException('优惠券不存在');
        }

        $record->checkAvailable($request->user());

//        if (!$record->enabled) {
//            abort(404);
//        }
//
//        if ($record->total - $record->used <= 0) {
//            return response()->json(['msg' => '该优惠券已被兑完'], 403);
//        }
//
//        if ($record->not_before && $record->not_before->gt(Carbon::now())) {
//            return response()->json(['msg' => '该优惠券现在还不能使用'], 403);
//        }
//
//        if ($record->not_after && $record->not_after->lt(Carbon::now())) {
//            return response()->json(['msg' => '该优惠券已过期'], 403);
//        }

        return $record;
    }
}
