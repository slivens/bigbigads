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
        $bookmarkListUsage = Auth::user()->getUsage('bookmark_list');
        $bookmarkLists = Bookmark::where("uid", Auth::user()->id)->get();
        $bookmarkListCount = Bookmark::where("name", $request->name)
                                       ->where("uid", Auth::user()->id)
                                       ->count();
        if (count($bookmarkLists) >= $bookmarkListUsage[1]) {
            return $this->responseError(trans('messages.bookmark_num_limit'), -4498);
        }
        if ($bookmarkListCount > 0) {
            return $this->responseError(trans('messages.bookmark_existed'), -4496);
        }
        $validator = Validator::make($request->all(), ['name' => 'required|between:1,25']);
        if ($validator->fails()) 
        {
            return $this->responseError(trans('messages.bookmark_length_limit'), -4497);
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
            return response(["code"=>-1, "desc"=>"No Permission"], 422);
        }
        $bookmark = Bookmark::where("id", $id)->first();
        if (!($bookmark instanceof Bookmark)) {
            return $this->responseError(trans('messages.bookmark_list_no_find'), -4495);
        }
        /*if (!Auth::user()->can('update', $bookmark)) {
            return response(["code"=>-1, "desc"=>"No Permission"], 501);
        }*/
        //限制目录名称长度
        $validator = Validator::make($req->all(), ['name' => 'required|between:1,25']);
        if ($validator->fails()) 
        {
            return $this->responseError(trans('messages.bookmark_length_limit'), -4497);
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
        if (!($bookmark instanceof Bookmark)) {
            return $this->responseError(trans('messages.bookmark_list_no_find'), -4495);
        }
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
