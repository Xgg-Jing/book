<?php

namespace App\Http\Controllers\Service;

use App\Entity\Member;
use App\Entity\TempEmail;
use App\Http\Controllers\Controller;

use App\Models\M3Email;
use App\Models\M3Result;
use App\Tool\UUID;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;


class MemberController extends Controller
{
    public function register(Request $request){

        $params = $request->all();
//        print_r($params);die;
        $email=$params['email'];
        $phone=$params['phone'];
        $password=$params['password'];
        $confirm=$params['confirm'];
        $phone_code=$params['phone_code'];
        $validate_code=$params['validate_code'];
//        print_r($params);die;



        $m3_result = new M3Result();
//print_r(strlen($validate_code != 4));die;
        if($validate_code == '' || strlen($validate_code == 4) ){
            $m3_result->status = 6;
            $m3_result->message = '验证码为4位';
            return $m3_result->toJson();
        }

        $validate_code_session = $request->session()->get('validate_code', '');
        if($validate_code_session != $validate_code) {
            $m3_result->status = 8;
            $m3_result->message = '验证码不正确';
            return $m3_result->toJson();
        }

        $member = new Member();
        $member->email = $email;
        $member->password = md5('bk' . $password);
        $member->save();

        $uuid = UUID::create();

        $m3_email = new M3Email;
        $m3_email->to = $email;
        $m3_email->cc = '309300232@qq.com';
        $m3_email->subject = '凯恩书店验证';
        $m3_email->content = '请于24小时点击该链接完成验证. http://book.com/service/validate_email'
            . '?member_id=' . $member->id
            . '&code=' . $uuid;

        $tempEmail = new TempEmail();
        $tempEmail->member_id = $member->id;
        $tempEmail->code = $uuid;
        $tempEmail->deadline = date('Y-m-d H-i-s', time() + 24*60*60);
        $tempEmail->save();

        Mail::send('email_register', ['m3_email' => $m3_email], function ($m) use ($m3_email) {
            // $m->from('hello@app.com', 'Your Application');
            $m->to($m3_email->to, '尊敬的用户')
                ->cc($m3_email->cc)
                ->subject($m3_email->subject);
        });

        $m3_result->status=0;
        $m3_result->message='注册成功';
        return $m3_result->toJson();
    }
    public function login(Request $request){
        $params = $request->all();
        $email=$params['username'];
        $password=$params['password'];
        $validate_code=$params['validate_code'];
//print_r($params);die;
        $m3_result=new M3Result();
        if($validate_code!=$request->session()->get('validate_code')){
            $m3_result->status=1;
            $m3_result->message='验证码不正确';
            return $m3_result->toJson();
        }
        $member=Member::where('email',$email)->first();
        if($member==null){
            $m3_result->status=2;
            $m3_result->message='用户不存在';
            return $m3_result->toJson();
        }else{
            if (md5('bk' . $password)!=$member->password){
                $m3_result->status=3;
                $m3_result->message='密码不正确';
                return $m3_result->toJson();
            }
        }

        $request->session()->put('member',$member);
        $m3_result->status=0;
        $m3_result->message='登录成功';
        return $m3_result->toJson();

    }
}
