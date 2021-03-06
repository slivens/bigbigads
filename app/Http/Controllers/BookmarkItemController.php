<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Bookmark;
class BookmarkItemController extends Controller
{
    protected $name;
    protected $class;
    public function __construct()
    {
        $this->middleware('auth');
        $this->name = str_singular(studly_case(explode(".", Route::currentRouteName())[0]));
        $this->class = "App\\" . $this->name;
    }

    /*
     * 显示资源（强制指定uid，简化权限判断,牺牲了一定的灵活性,可考虑创建中间件抛异常的方法)
     */
    public function index(Request $req)
    {
        $class = $this->class;
        $obj = new $class;

        if (isset($req->where)) {
            if (is_string($req->where))
                $obj = $obj->where(json_decode($req->where));
            else
                $obj = $obj->where($req->where);
        }
        $obj = $obj->where('uid', '=', Auth::user()->id);
        $items = $obj->get();
        return $items;
    }

    /**
     * 创建新收藏表单页面
     */
    public function create()
    {
    }

    /**
     * 存储资源
     */
    public function store(Request $request)
    {
        //按权限限制收藏广告
        $saveAdCount = Auth::user()->getUsage('save_ad_count');
        $class = $this->class;
        $item = $class::where("uid", Auth::user()->id)->get(); 
        if (count($item) >= $saveAdCount[1]) {
            return $this->responseError(trans('messages.bookmark_num_limit'), -4499);
        }
        $all = $request->all();
        $all['uid'] = Auth::user()->id;
        $item = $class::create($all);
        return $item;
    }

    /**
     * 显示指定收藏
     */
    public function show($id)
    {
        $class = $this->class;
        $item = $class::where("id", $id)->first();
        return $item;
    }

    /**
     * 编辑指定收藏
     */
    public function update(Request $req, $id)
    {
        $bookmark = Bookmark::where("id", $id)->first();
        if (!($bookmark instanceof Bookmark)) {
            return $this->responseError(c, -4495);
        }
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
     * 删除指定项
     */
    public function destroy($id)
    {
        $class = $this->class;
        $item = $class::where("id", $id)->first();
        /*if (!Auth::user()->can('delete', $item)) {
            return response(["code"=>-1, "desc"=>"No Permission"], 501);
        } */
        $item->delete();
        return ["code"=>0, "desc"=>"Success"];
    }

}
