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
    public function index()
    {
        return Bookmark::all();
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
    public function edit($id)
    {

    }

    /**
     * 删除指定收藏
     */
    public function destroy($id)
    {
        $bookmark = Bookmark::where("id", $id)->first();
        if (!Auth::user()->can('delete', $bookmark)) {
            return response(["code"=>-1, "desc"=>"No Permission"], 501);
        }
        $bookmark->delete();
        return ["code"=>0, "desc"=>"Success"];
    }
}
