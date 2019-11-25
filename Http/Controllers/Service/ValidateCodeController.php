<?php

namespace App\Http\Controllers\Service;

use App\Http\Controllers\Controller;
use App\Tool\Validate\ValidateCode;
use Illuminate\Http\Request;

class ValidateCodeController extends Controller
{
    public function create(Request $request){
        $validateCode=new ValidateCode();
        $request->session()->put('validate_code',$validateCode->getCode());
        return $validateCode->doimg();
    }
}
