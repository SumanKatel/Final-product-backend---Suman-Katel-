@extends('admin.master')
@section('title', $page_header)
@section('content-header', $page_header)
@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">List of Category</div>
            <div class="card-body table-responsive">
                <table class="table table-hover table-sm dataTablePagination compact">
                    <thead class="bg-primary">
                        <tr>
                            <th width="10px">S.No</th>
                            <th>Title (English)</th>
                            <th>Slug</th>
                            {{-- <th>Link</th> --}}
                            <th width="50px">Status</th>
                            <th width="50px">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $count = 1; ?>
                        @foreach ($list as $item)
                        <tr>
                            <td>{{ $count++ }}</td>
                            <td>{{ $item->title }}</td>
                            <td>{{ $item->slug }}</td>
                            {{-- <td><a href="{{ route('category',$item->slug) }}" target="_blank">{!! LINK_ICON !!}</a></td> --}}
                            <td class="center-align">
                                @if ($item->status == '1')
                                {!! ACTIVE_STATUS !!}
                                @else
                                {!! INACTIVE_STATUS !!}
                                @endif
                            </td>
                            <td width="100px" class="center-align">
                                <a href="{{ route($link.'.edit', $item->id) }}">{!! EDIT_ICON !!} </a>&nbsp;|&nbsp;
                                <a href="{{ route($link.'.delete', $item->id) }}" class="resetbtn">{!! DELETE_ICON !!}</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">{{ $page_header }}</div>
            <div class="card-body">
                <form class="" method="POST" action="{{ route($link.'.store') }}">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label for="title">Title (English) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="title" name="title">
                        @if ($errors->has('title'))
                            <span class="help-block">
                                <strong>{{ $errors->first('title') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="description">Description (English)</label>
                        <input type="text" class="form-control" id="description" name="description">
                        @if ($errors->has('description'))
                            <span class="help-block">
                                <strong>{{ $errors->first('description') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select name="status" id="statusid" class="form-control">
                            <option value="1">{!! PUBLISH !!}</option>
                            <option value="0">{!! UNPUBLISH !!}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-success">Submit</button>
                        <button type="reset" class="btn btn-danger">Reset</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection