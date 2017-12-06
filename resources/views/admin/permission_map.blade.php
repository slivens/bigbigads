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
                                    <?php
                                      $hasPolicy = isset($role->groupedPolicies[$permission->key])
                                    ?>
                                    <tr>
                                        <td>
                                            {{$permission->table_name}} - {{$permission->key}}
                                            @if ($hasPolicy)
                                                <div class="form-group text-center small">
                                                    <label>策略类型:</label>
                                                    <select class="form-control" name="policy[]" readonly>
                                                        @foreach ($types as $key => $type)
                                                        <option value="{{$key}}" @if ($hasPolicy && $key == $role->groupedPolicies[$permission->key][0]) selected @endif>{{$type}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            @endif
                                        </td>
                                        @foreach ($roles as $role)
                                        <td>  
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" value="{{$permission->id}}" name="{{$role->name . '_permission' . '[' . $permission->key . ']'}}" @if ($role->can($permission->key)) checked @endif/>
                                                </label>
                                            </div> 
                                            @if ($groupedPolicies->has($permission->key))
                                            <div class="small">
                                                <div class="text-center">策略值</div>
                                                <div class="form-group">
                                                    <label>数值:</label>
                                                    <input class="form-control" type="text" name="{{$role->name . '_policy' . '['. $permission->key . ']' . '[value]'}}" @if (isset($role->groupedPolicies[$permission->key]))  value="{{$role->groupedPolicies[$permission->key][1]}}" @endif />
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
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">更新权限</button>
                        </div>
                        </form>
                        <!-- <form action="{{route('generate_permission_cache')}}" method="POST"> -->
                        <!--     {{ csrf_field() }} -->
                        <!--     <button type="submit" class="btn btn-success">重新生成角色缓存</button> -->
                        <!-- </form> -->
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
