@extends('admin.master')
@section('title', $page_header)
@section('content-header', $page_header)
@section('content')
<div class="card">
    <div class="card-header">{{ $page_header }}
        <div class="card-header-actions">
            <a class="card-header-action btn btn-warning" href="{{ route($link.'.list') }}" target="_blank">
                <small class="text-muted">{!! VIEWLIST_ICON !!}</small>
            </a>
        </div>
    </div>
    <form class="form-horizontal" method="POST" action="{{ route('user.update', $record->id) }}">
        <div class="card-body">
            {{ csrf_field() }}
            {{ method_field('PATCH') }}
            <div class="form-group row">
                <label class="col-md-3 col-form-label" for="select1">Admin Role Type</label>
                <div class="col-md-9">
                    <select name="user_group_id" id="user_group_id" class="form-control">
                        @if(!empty($admingroup))
                        @foreach($admingroup as $list)
                        <option value = {{ $list->id }} @if($list->id == $record->user_group_id) selected @endif>{{ $list->title }}</option>
                        @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label" for="text-input">Name</label>
                <div class="col-md-9">
                    <input id="name" type="text" class="form-control" placeholder="Enter Full Name " name="name" value="{{ $record->name }}" autofocus>
                    @if ($errors->has('name'))
                    <span class="help-block text-danger">{{ $errors->first('name') }}</span>
                    @endif
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label" for="email-input">E-Mail Address</label>
                <div class="col-md-9">
                    <input id="email" type="text" class="form-control" placeholder="Enter Email Address" name="email" value="{{ $record->email }}">
                    @if ($errors->has('email'))
                    <span class="help-block text-danger">{{ $errors->first('email') }}</span>
                    @endif
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label" for="email-input">Mobile Number</label>
                <div class="col-md-9">
                    <input id="mobileno" type="number" class="form-control" placeholder="Enter Mobile Number" name="mobileno" value="{{ $record->mobileno }}" >
                    @if ($errors->has('mobileno'))
                    <span class="help-block text-danger">{{ $errors->first('mobileno') }}</span>
                    @endif
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label" for="email-input">UserName</label>
                <div class="col-md-9">
                    <input id="username" type="text" class="form-control" placeholder="Enter UserName" autocomplete="off" name="username" value="{{ $record->username }}" >
                    @if ($errors->has('username'))
                    <span class="help-block text-danger">{{ $errors->first('username') }}</span>
                    @endif
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label" for="email-input">&nbsp;</label>
                <div class="col-md-9">
                    <input id="changepwd" type="checkbox" name="changepwd"> Change Password
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label" for="password-input">Password</label>
                <div class="col-md-9">
                    <input id="password" type="password" class="form-control" placeholder="Enter Password" autocomplete="off" disabled name="password">
                    @if ($errors->has('password'))
                    <span class="help-block text-danger">{{ $errors->first('password') }}</span>
                    @endif
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-form-label" for="password-input">Confirm Password</label>
                <div class="col-md-9">
                    <input id="password-confirm" type="password" class="form-control" placeholder="Enter Confirm Password" disabled name="password_confirmation">
                </div>
            </div>
            @if($record->id != '1')
            <div class="form-group row">
                <label class="col-md-3 col-form-label" for="select2">Status</label>
                <div class="col-md-9">
                    <select name="status" id="statusid" class="form-control">
                        <option value="1">{!! PUBLISH !!}</option>
                        <option value="0">{!! UNPUBLISH !!}</option>
                    </select>
                </div>
            </div>
            @endif
        </div>
        <div class="card-footer">
            <button class="btn btn-sm btn-primary" type="submit">
            <i class="fa fa-dot-circle-o"></i> Update</button>
            <button class="btn btn-sm btn-danger resetbtn" type="reset">
            <i class="fa fa-ban"></i> Reset</button>
        </div>
    </form>
</div>
@push('script')
<script type="text/javascript">
$(document).ready(function() {
  $('#changepwd').removeAttr('checked');
  $('#changepwd').click(function () {
    //check if checkbox is checked
    if ($(this).is(':checked')) {
      $('#password').removeAttr('disabled'); //enable input
      $('#password-confirm').removeAttr('disabled'); //enable input
    } else {
      $('#password').attr('disabled', true); //disable input
      $('#password-confirm').attr('disabled', true); //disable input
    }
  });
});
</script>
@endpush
@endsection