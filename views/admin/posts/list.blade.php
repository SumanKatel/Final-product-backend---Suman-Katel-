@extends('admin.master')
@section('title', $page_header)
@section('content-header', $page_header)
@section('content')
<div class="card">
    <div class="card-header">{{ $page_header }}
        <div class="card-header-actions">
            <a class="card-header-action btn btn-warning" href="{{ route($link.'.create') }}">
                <small class="text-muted">{!! ADD_ICON !!}</small>
            </a>
        </div>
    </div>
    <div class="card-body">
        <form class="">
            <div class="row">
                <div class="col-md-4">
                <div class="form-group">
                    <label>Title (English)</label>
                    <input type="text" class="form-control" id="title" name="title" value="{{ isset($_GET['title'])?$_GET['title']:'' }}" placeholder="Search By Title (English)">
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label>Date</label>
                    <input type="text" class="form-control datepicker" id="published_date" name="published_date" value="{{ isset($_GET['published_date'])?$_GET['published_date']:'' }}" placeholder="Search By Date">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Search By Category</label>
                    <select class="form-control" name="category" id="category">
                        <option value="">Search By Category</option>
                        @if(!empty($categorylist))
                            @foreach($categorylist as $cat)
                                <option value="{{ $cat->id }}" @if(isset($_GET['category']) && $_GET['category'] == $cat->id) {{ 'selected' }} @endif>{{ $cat->title }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label class="vi-hidden">x</label>
                    <button type="submit" class="btn btn-primary">Search</button>
                    <a href="{{ route($link.'.index') }}" class="btn btn-danger">Reset</a>
                </div>
            </div>
            </div>
        </form>
        <div class="clearfix"></div>
        <br>
        <table class="table table-hover table-sm">
            <thead class="bg-primary">
                <tr>
                    <th>S.No</th>
                    <th>Title</th>
                    {{-- <th>Open Link</th> --}}
                    <th class="text-center">Category</th>
                    <th class="text-center">Published Date</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                @if(!$list->isEmpty())
                {{-- @if($list) --}}
                <?php $count = ($list->currentpage()-1)*$list->perpage()+1; ?>
                @foreach ($list as $item)
                <tr>
                    <td>{{ $count++ }}</td>
                    <td>{{ str_limit($item->title, $limit = 100, $end = '...') }}</td>
                    {{-- <td class="text-center"><a href="{{ route('post.detail',$item->slug) }}" target="_blank"><i class="fa fa-external-link" aria-hidden="true"></i></a></td> --}}
                    <td class="text-center">
                        @if(!empty($item->category))
                        <ul class="list-unstyled">
                            @foreach($item->category as $cat)
                            <span class="label label-default">{{ $cat->title }}</span>
                            @endforeach
                        </ul>
                        @endif
                    </td>
                    <td class="text-center">{{ $item->published_date }}</td>
                    <td class="text-center">
                        @if ($item->status == '1')
                        {!! ACTIVE_STATUS !!}
                        @else
                        {!! INACTIVE_STATUS !!}
                        @endif
                    </td>
                    <td class="text-center">
                        <a href="{{ route($link.'.edit', $item->id) }}"> {!! EDIT_ICON !!}</a>&nbsp;|
                        <a href="{{ route($link.'.delete', $item->id) }}" class="resetbtn">{!! DELETE_ICON !!} </a>
                    </td>
                </tr>
                @endforeach
                @else
                <tr class="text-center">
                    <td colspan="7">{!! NO_RECORD !!}</td>
                </tr>
                @endif
            </tbody>
        </table>
        {{ $list
            ->appends(Request::only('published_date'))
            ->appends(Request::only('category'))
            ->appends(Request::only('title'))
            ->links()
        }}
    </div>
</div>
@endsection