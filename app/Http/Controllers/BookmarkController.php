<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Bookmark;

class BookmarkController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    /*
     * 显示收藏目录
     */
    public function index(Request $req)
    {
        //显示收藏夹之前需要检查是否为合法用户
        if (intval($req->uid) != Auth::user()->id) {
            return response(["code"=>-1, "desc"=>"No Permission"], 501);
        }
        return Bookmark::where("uid", $req->uid)->get();
    }

    /**
     * 创建新收藏表单页面
     */
    public function create()
    {
    }

    /**
     * 存储收藏
     */
    public function store(Request $request)
    {
        $bookmark = new Bookmark();
        $bookmark->uid = Auth::user()->id;
        $bookmark->name = $request->name;
        $bookmark->save();
        return $bookmark;
    }

    /**
     * 显示指定收藏
     */
    public function show($id)
    {
    }

    /**
     * 编辑指定收藏
     */
    public function update(Request $req, $id)
    {
        //编辑收藏夹之前需要检查是否为合法用户
        if (intval($req->uid) != Auth::user()->id) {
            return response(["code"=>-1, "desc"=>"No Permission"], 501);
        }
        $bookmark = Bookmark::where("id", $id)->first();
        /*if (!Auth::user()->can('update', $bookmark)) {
            return response(["code"=>-1, "desc"=>"No Permission"], 501);
        }*/
        $update = false;
        foreach($req->all() as $key=>$value) {
            if (isset($bookmark[$key]) && $value != $bookmark->$key) {
               $bookmark->$key = $value;
               $update = true;
            }
        }
        $bookmark->save();
        return $bookmark;
    }

    /**
     * 删除指定收藏
     */
    public function destroy($id)
    {
        //删除收藏夹之前需要检查是否为合法用户
        if ($id != Auth::user()->id) {
            return response(["code"=>-1, "desc"=>"No Permission"], 501);
        }
        $bookmark = Bookmark::where("id", $id)->first();
        /*if (!Auth::user()->can('delete', $bookmark)) {
            return response(["code"=>-1, "desc"=>"No Permission"], 501);
        } */
        $items = $bookmark->items;
        foreach ($items as $item) {
            $item->delete();
        }
        $bookmark->delete();
        return ["code"=>0, "desc"=>"Success"];
    }
}
