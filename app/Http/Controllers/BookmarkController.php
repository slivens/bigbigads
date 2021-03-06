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
    public function index()
    {
        //显示收藏夹之前需要检查是否为合法用户
        if ($user = Auth::user()) {
            return Bookmark::where("uid", $user->id)->get();
        }
        return $this->responseError("No Permission", -1);
        
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
        $user = Auth::user();
        $bookmarkListUsage = $user->getUsage('bookmark_list');
        $bookmarkLists = Bookmark::where("uid", $user->id)->get();
        $bookmarkListCount = $user->bookmarks()->where("name", $request->name)
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
        if (!$bookmark->canModify()) {
            return $this->responseError(trans('messages.bookmark_can_not_modify'), -4500);
        }
        $bookmark->uid = $user->id;
        $bookmark->name = $request->name;
        $bookmark->default = 0; // 用户自行加入的
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
        $user = Auth::user();
        if (intval($req->uid) != $user->id) {
            return response(["code"=>-1, "desc"=>"No Permission"], 422);
        }
        $bookmark = Bookmark::where("id", $id)->first();
        if (!($bookmark instanceof Bookmark)) {
            return $this->responseError(trans('messages.bookmark_list_no_find'), -4495);
        }
        /*if (!Auth::user()->can('update', $bookmark)) {
            return response(["code"=>-1, "desc"=>"No Permission"], 501);
        }*/
        if (!$bookmark->canModify()) {
            return $this->responseError(trans('messages.bookmark_can_not_modify'), -4500);
        }
        // 同一用户名下不允许有2个同名收藏夹
        if ($user->bookmarks()->where("name", $req->name)->where('id', '<>', $req->id)->first()) {
            return $this->responseError(trans('messages.bookmark_existed'), -4496);
        }
        // 限制目录名称长度
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
        if (!$bookmark->canModify()) {
            return $this->responseError(trans('messages.bookmark_can_not_modify'), -4500);
        }
        $items = $bookmark->items;
        foreach ($items as $item) {
            $item->delete();
        }
        $bookmark->delete();
        return ["code"=>0, "desc"=>"Success"];
    }

    public function getDefault()
    {
        if ($user = Auth::user()) {
            return $user->bookmarks()->where('default', 1)->first();
        } else {
            return $this->responseError(trans('messages.bookmark_list_no_find'), -4495);
        }
    }
}
