<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Log;

class ResourceController extends Controller
{
    protected $name;
    protected $class;
    protected $where = null;

    public function __construct(Request $request)
    {
        $this->name = str_singular(studly_case(basename($request->path())));
        $this->class = "App\\" . $this->name;
        if ($request->has('where'))
            $this->where = json_decode($request->where, true);
    }

    protected function checkBeforeIndex(Request $request)
    {
        return true;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $class = $this->class;
        try {
            if (!$this->checkBeforeIndex($request))
                return [];
            if ($this->where) {
                return $class::where($this->where)->get();
            }
        } catch(\Exception $e) {
            return $this->responsesError($e->getMessage(), $e->getCode());
        }
        return $class::all();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($this->name == 'Comment') {
            $user = Auth::user()->userinfo;
            $typeid = intval($request->typeid);
            $data = $request->all();
            $data['userid'] = $user->uid;
            $data['username'] = $user->username;
            if ($typeid >= 5 && $typeid <= 9)
                $data['type'] = "公司评价";
            $data['ischeck'] = 0;
            $data['nm'] = 0;
            //VIP直接发布，不用等待审核
            if (intval($user->vip) == 1) {
                $data['ischeck'] = 1;
            }
            $data['addtime'] = Carbon::now()->toDateTimeString();
        }
        $class = $this->class;
        $item = $class::create($data);
        return $item;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
