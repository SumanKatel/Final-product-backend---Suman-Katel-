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
      <div class="card-body table-responsive">
        <table class="table table-hover table-sm dataTable compact">
            <thead class="bg-primary">
                <tr>
                    <th>S.No</th>
                    <th>UserName</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th class="text-center">Mobile No</th>
                    <th class="text-center">Last Login Date</th>
                    <th class="text-center">Created Date</th>
                    <th class="text-center">Admin Roles</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php $count = 1; ?>
                @if($list)
                @foreach ($list as $item)
                <tr>
                    <td>{{ $count++ }}</td>
                    <td>{{ $item->username }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->email }}</td>
                    <td class="text-center">{{ $item->mobileno }}</td>
                    <td class="text-center">{{ $item->lastlogin }}</td>
                    <td class="text-center">{{ $item->created_at }}</td>
                    <td class="text-center">{{ $item->admingroup->title }}</td>
                    <td class="text-center">
                        @if ($item->status == '1')
                        {!! ACTIVE_STATUS !!}
                        @else
                        {!! INACTIVE_STATUS !!}
                        @endif
                    </td>
                    <td width="100px" class="text-center">
                        <a href="{{ route($link.'.edit',$item->id) }}">{!! EDIT_ICON !!} </a>
                        @if($item->id != '1')
                        <a href="{{ route($link.'.delete', $item->id) }}"  class="resetbtn">&nbsp; | &nbsp; {!! DELETE_ICON !!}</a>
                        @endif
                    </td>
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection