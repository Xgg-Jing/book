<?php


namespace App\Http\Controllers\Service;


use App\Entity\Category;
use App\Http\Controllers\Controller;
use App\Models\M3Result;

class BookController extends Controller
{
    public function getCategoryParentId($parent_id){
        $categorys=Category::where('parent_id',$parent_id)->get();

        $m3_result=new M3Result();
        $m3_result->status=0;
        $m3_result->message='成功';
        $m3_result->categorys=$categorys;

        return $m3_result->toJson();
    }
}
