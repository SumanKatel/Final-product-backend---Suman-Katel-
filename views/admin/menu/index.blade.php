@extends('admin.master')

@section('content')
    {!! Menu::render() !!}
@endsection

{{-- //YOU MUST HAVE JQUERY LOADED BEFORE menu scripts
 --}}@push('script')
    {!! Menu::scripts() !!}
@endpush