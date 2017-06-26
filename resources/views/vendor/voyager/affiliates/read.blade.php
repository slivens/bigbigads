@extends('voyager::master')

@section('page_title','View '.$dataType->display_name_singular)

@section('page_header')
    <h1 class="page-title">
        <i class="{{ $dataType->icon }}"></i> Viewing {{ ucfirst($dataType->display_name_singular) }} &nbsp;

        <a href="{{ route('voyager.'.$dataType->slug.'.edit', $dataTypeContent->getKey()) }}" class="btn btn-info">
            <span class="glyphicon glyphicon-pencil"></span>&nbsp;
            Edit
        </a>
    </h1>
@stop

@section('content')
    <div class="page-content container-fluid">
        <div class="row">
            <div class="col-md-12">

                <div class="alert alert-info">
联盟链接:<br/>
@if($dataTypeContent->status > 0)
App: <br/>{{env('APP_URL') . '?track=' . $dataTypeContent->track}} <br/>

Home: <br/>{{env('APP_URL') . 'home?track=' . $dataTypeContent->track}}
@else
<span class="label label-danger">禁用状态</span>
@endif
</div>
                <div class="panel panel-bordered" style="padding-bottom:5px;">

                    <!-- /.box-header -->
                    <!-- form start -->

                    @foreach($dataType->readRows as $row)
                        @php $rowDetails = json_decode($row->details); @endphp

                        <div class="panel-heading" style="border-bottom:0;">
                            <h3 class="panel-title">{{ $row->display_name }}</h3>
                        </div>

                        <div class="panel-body" style="padding-top:0;">
                            @if($row->type == "image")
                                <img class="img-responsive"
                                     src="{{ Voyager::image($dataTypeContent->{$row->field}) }}">
                            @elseif($row->type == 'select_dropdown' && property_exists($rowDetails, 'options') &&
                                    !empty($rowDetails->options->{$dataTypeContent->{$row->field}})
                            )

                                <?php echo $rowDetails->options->{$dataTypeContent->{$row->field}};?>
                            @elseif($row->type == 'select_dropdown' && $dataTypeContent->{$row->field . '_page_slug'})
                                <a href="{{ $dataTypeContent->{$row->field . '_page_slug'} }}">{{ $dataTypeContent->{$row->field}  }}</a>
                            @elseif($row->type == 'select_multiple')
                                @if(property_exists($rowDetails, 'relationship'))

                                    @foreach($dataTypeContent->{$row->field} as $item)
                                        @if($item->{$row->field . '_page_slug'})
                                        <a href="{{ $item->{$row->field . '_page_slug'} }}">{{ $item->{$row->field}  }}</a>@if(!$loop->last), @endif
                                        @else
                                        {{ $item->{$row->field}  }}
                                        @endif
                                    @endforeach

                                @elseif(property_exists($rowDetails, 'options'))
                                    @foreach($dataTypeContent->{$row->field} as $item)
                                     {{ $rowDetails->options->{$item} . (!$loop->last ? ', ' : '') }}
                                    @endforeach
                                @endif
                            @elseif($row->type == 'date')
                                {{ $rowDetails && property_exists($rowDetails, 'format') ? \Carbon\Carbon::parse($dataTypeContent->{$row->field})->formatLocalized($rowDetails->format) : $dataTypeContent->{$row->field} }}
                            @elseif($row->type == 'checkbox')
                                @if($rowDetails && property_exists($rowDetails, 'on') && property_exists($rowDetails, 'off'))
                                    @if($dataTypeContent->{$row->field})
                                    <span class="label label-info">{{ $rowDetails->on }}</span>
                                    @else
                                    <span class="label label-primary">{{ $rowDetails->off }}</span>
                                    @endif
                                @else
                                {{ $dataTypeContent->{$row->field} }}
                                @endif
                            @elseif($row->type == 'radio_btn')
                                @php $key = "" . $dataTypeContent->{$row->field} @endphp
                                @foreach ($rowDetails->options as $option) 
                                    @if($dataTypeContent->{$row->field} == $loop->index)
                                        <p>{{ $option}}</p>
                                    @endif
                                @endforeach
                            @elseif($row->type == 'rich_text_box')
                                <p>{{ strip_tags($dataTypeContent->{$row->field}, '<b><i><u>') }}</p>
                            @else
                                <p>{{ $dataTypeContent->{$row->field} }}</p>
                            @endif
                        </div><!-- panel-body -->
                        @if(!$loop->last)
                            <hr style="margin:0;">
                        @endif
                    @endforeach

                </div>

                <div class="panel panel-bordered" style="padding-bottom:5px;">
                    <div class="panel-heading" style="border-bottom:0;">
                        <h3 class="panel-title">注册用户({{count($dataTypeContent->users)}})</h3>
                    </div>
                    <div class="panel-body" style="padding-top:0;">
                        <table class="table table-striped table-bordered">
<tr>
    <th>email</th>
    <th>名称</th>
    <th>注册IP</th>
    <th>注册时间</th>
</tr>
                        @foreach($dataTypeContent->users as $user)
<tr>
    <td>{{$user->email}}</td>
    <td>{{$user->name}}</td>
    <td>{{$user->regip}}</td>
    <td>{{$user->created_at}}</td>
</tr>
                        @endforeach
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('javascript')
<script type="text/javascript">
console.log("in voyager");
</script>
@stop
