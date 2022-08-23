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
    <form class="" method="POST" action="{{ route('faq.store') }}">
            {{ csrf_field() }}
            <table id="sortable" class="table table-striped table-hover todo-list ui-sortable" >
                <tr class="nodrag nodrop">
                    <th><span class="handle"><i class="fa fa-arrows"></i></span></th>
                    <th>Title English</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Action</th>
                </tr>
                <?php $sort_orders = ''; ?>
                @if(!empty($list))
                @foreach ($list as $item)
                <?php $sort_orders .= $item->sort_order . ','; ?>
                <tr id="{{ $item->id }}">
                    <td class="move">
                        <span class="handle">
                            <i class="fa fa-ellipsis-v"></i>
                            <i class="fa fa-ellipsis-v"></i>
                        </span>
                    </td>
                    <td>{{ $item->title }}</td>
                    <td class="text-center">
                        @if ($item->status == '1'){!! ACTIVE_STATUS !!} @else {!! INACTIVE_STATUS !!} @endif
                    </td>
                    <td class="text-center">
                        <a href="{{ route($link.'.edit', $item->id) }}"> {!! EDIT_ICON !!}</a>&nbsp;|
                        <a href="{{ route($link.'.delete', $item->id) }}" class="resetbtn">{!! DELETE_ICON !!} </a>
                    </td>
                </tr>
                @endforeach
                @else
                <tr>
                    <td colspan="5">{!! NO_RECORD !!}</td>
                </tr>
                @endif
            </table>
        </form>
    </div>
</div>
@push('script')
    <script type="text/javascript">
        $('#sortable').tableDnD({
        onDrop: function (table, row) {
            $.post("{{ route('ajax.sorting') }}", {ids_order: $.tableDnD.serialize(), sort_orders: '<?php echo $sort_orders; ?>', table: 'tbl_faq', _token: '{!! csrf_token() !!}'}, function(data){
                console.log('data');
            });
        }
    });
    </script>
@endpush
@endsection