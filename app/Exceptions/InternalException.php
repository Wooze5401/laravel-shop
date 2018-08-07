<?php

namespace App\Exceptions;

use Exception;
use Throwable;
use Illuminate\Http\Request;

class InternalException extends Exception
{
    protected $messageForUser;

    public function __construct($message = "", $messageForUser = "系统内部错误", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->messageForUser = $messageForUser;
    }

    public function render(Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json(['msg' => $this->messageForUser], $this->code);
        }

        return view('pages.error', ['msg' => $this->messageForUser]);
    }
}
