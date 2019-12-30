<?php

namespace App\Exceptions;

use App\Http\Requests\Request;
use Exception;

class InternalException extends Exception
{
    /**
     * @var string
     */
    private $msgForUser;

    public function __construct($message = "", $code = 500, $msgForUser = '系统内部错误')
    {
        parent::__construct($message, $code);
        $this->msgForUser = $msgForUser;
    }

    public function render(Request $request){
        if ($request->expectsJson()) {
            return response()->json(['msg' => $this->msgForUser],$this->code);
        }

        return view('pages.error',['msg' => $this->msgForUser]);
    }
}
