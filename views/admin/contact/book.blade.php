@extends('admin.master')
@section('title', $page_header)
@section('content-header', $page_header)
@section('content')
<div class="card">
    <div class="card-header">{{ $page_header }}
        <div class="card-header-actions">
        </div>
    </div>
    <div class="card-body">
            <table id="sortable" class="table table-striped table-hover todo-list ui-sortable" >
                <tr class="nodrag nodrop">
                    <th>S.No</th>
                    <th>Product Name</th>
                    <th>Customer Name</th>
                    <th>Address</th>
                    <th>Order date</th>
                </tr>
                <?php $count = 1; ?>
                @foreach ($list as $item)
                <?php 
                $product = \App\Model\admin\Product::find($item->product_id);
                $customer = \App\Model\admin\Customer::find($item->customer_id);
                ?>
                <tr>
                    <td>{{ $count++ }}</td>
                    <td>{{ $product->title }}</td>
                    <td>{{ $customer->name }}</td>
                    <td>{{$item->address}}</td>
                    <td>{{ $item->created_at }}</td>
                   
                </tr>
                @endforeach
            </table>
    </div>
</div>
@endsection