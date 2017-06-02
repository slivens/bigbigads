<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Bookmark;
use Illuminate\Support\Facades\Validator;

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
            return $this->responseError("No Permission", -1);
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
        /*
            创建收藏类别表
            按权限限制收藏夹目录数量及检测有效性
        */
        $bookmark = new Bookmark();
        $bookmark_list_usage = Auth::user()->getUsage('bookmark_list');
        $bookmark_lists = Bookmark::where("uid", Auth::user()->id)->get();
        $bookmark_list_count = Bookmark::where("name", $request->name)
                                       ->where("uid", Auth::user()->id)
                                       ->count();
        if (count($bookmark_lists) >= $bookmark_list_usage[1]) {
            return $this->responseError("You've reached your bookmark list limit. Upgrade your account to see more", -4498);
        }
        if ($bookmark_list_count > 0) {
            return $this->responseError("the bookmark list name had exist", -4496);
        }
        $validator = Validator::make($request->all(), ['name' => 'required|between:1,25']);
        if ($validator->fails()) 
        {
            return $this->responseError("The bookmark list name must be at least 1 characters and no more 25 characters. ", -4497);
        }
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
        //限制目录名称长度
        $validator = Validator::make($req->all(), ['name' => 'required|between:1,25']);
        $bookmark_list_count = Bookmark::where("name", $req->name)
                                       ->where("uid", Auth::user()->id)
                                       ->count();
        if ($validator->fails()) 
        {
            return $this->responseError("The bookmark list name must be at least 1 characters and no more 25 characters. ", -4497);
        }
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
        //该id是广告id
        $bookmark = Bookmark::where("id", $id)->first();
        if ($bookmark->uid != Auth::user()->id) {
            return $this->responseError("No Permission", -1);
        }
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
