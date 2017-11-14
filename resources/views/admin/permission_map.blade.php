@extends('voyager::master')

@section('page_title','权限视图')

@section('page_header')
    <h1 class="page-title">
         权限视图
    </h1>
@stop

@section('content')
    <div class="page-content container-fluid">
        @include('voyager::alerts')
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body table-responsive">

                    <form role="form"
                            action="{{ route('store_permission_map') }}"
                            method="POST">
                        {{ csrf_field() }}
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>权限组-权限名称</th>
                                @foreach ($roles as $role)
                                    <th> {{$role->display_name}}</th>
                                @endforeach 
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($groupedPermissions as $group)
                                    @foreach ($group as $permission) 
                                    <tr>
                                        <td>{{$permission->table_name}} - {{$permission->key}}</td>
                                        @foreach ($roles as $role)
                                        <td>  
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" value="{{$permission->key}}" name="{{$role->name}}[]" @if ($role->can($permission->key)) checked @endif/>
                                                </label>
                                            </div> 
                                            @if ($groupedPolicies->has($permission->key))
                                            <?php
                                              $hasPolicy = isset($role->groupedPolicies[$permission->key])
                                            ?>
                                            <div class="small">
                                                <div class="text-center">策略</div>
                                                <div class="form-group">
                                                    <label>类型:</label>
                                                    <select name="{{$role->name . $permission->key . '_type'}}">
                                                        @foreach ($types as $key => $type)
                                                        <option value="{{$key}}" @if ($hasPolicy && $key == $role->groupedPolicies[$permission->key][0]) selected @endif>{{$type}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label>数值:</label>
                                                    <input class="form-control" type="text" name="{{$role->name . $permission->key . '_value'}}" @if (isset($role->groupedPolicies[$permission->key]))  value="{{$role->groupedPolicies[$permission->key][1]}}" @endif />
                                                </div>
                                            </div>
                                            @endif
                                        </td>
                                        @endforeach
                                    </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                        <button type="submit" class="btn btn-primary">更新权限</button>

                        <button type="button" class="btn btn-success">重新生成角色缓存</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop

@section('css')
@stop

@section('javascript')
    <!-- DataTables -->    
@stop
