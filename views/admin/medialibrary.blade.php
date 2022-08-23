@extends('admin.master')
@section('title', $page_header)
@section('content-header', $page_header)
@section('content')

<iframe src="{{ url('/uploads/filemanager/dialog.php?type=1&field_id=image') }}" width="100%" height="500px"></iframe>
@endsection