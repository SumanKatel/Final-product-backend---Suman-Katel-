@extends('admin.master')
@section('title', $page_header)
@section('content-header', $page_header)
@section('content')
<div class="col-md-12">
    <div class="card">
        <form class="form-horizontal" method="POST" action="{{ route('userprofile.update', $record->id) }} ">
        <div class="card-header">{{ $page_header }}</div>
            <div class="card-body">
                {{ csrf_field() }}
                {{ method_field('PATCH') }}
                <div class="form-group row">
                    <label class="col-md-3 col-form-label" for="hf-email">Name</label>
                    <div class="col-md-6">
                        <input id="name" type="text" class="form-control" placeholder="Enter Full Name" name="name" value="{{ $record->name  }}" required autofocus>
                        @if ($errors->has('name'))
                            <span class="help-block">{{ $errors->first('name') }}</span>
                        @endif
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3 col-form-label" for="hf-email">E-Mail Address</label>
                    <div class="col-md-6">
                        <input id="email" type="text" class="form-control" placeholder="Enter E-Mail Address" name="email" value="{{ $record->email  }}" required autofocus>
                        @if ($errors->has('email'))
                            <span class="help-block">{{ $errors->first('email') }}</span>
                        @endif
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3 col-form-label" for="hf-email">Mobile Number</label>
                    <div class="col-md-6">
                        <input id="mobileno" type="text" class="form-control" placeholder="Enter Mobile Number" name="mobileno" value="{{ $record->mobileno  }}" required autofocus>
                        @if ($errors->has('mobileno'))
                            <span class="help-block">{{ $errors->first('mobileno') }}</span>
                        @endif
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3 col-form-label" for="hf-email">User Name</label>
                    <div class="col-md-6">
                        <input id="username" type="text" class="form-control" placeholder="Enter User Name" name="username" value="{{ $record->username  }}" required autofocus>
                        @if ($errors->has('username'))
                            <span class="help-block">{{ $errors->first('username') }}</span>
                        @endif
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3 col-form-label" for="hf-email">&nbsp;</label>
                    <div class="col-md-6">
                        <input id="changepwd" type="checkbox" name="changepwd"> Change Password
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3 col-form-label" for="hf-email">Password</label>
                    <div class="col-md-6">
                        <input id="password" type="password" class="form-control" placeholder="Enter Password" name="password" disabled required>
                        @if ($errors->has('password'))
                        <span class="help-block">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3 col-form-label" for="hf-email">Confirm Password</label>
                    <div class="col-md-6">
                        <input id="password-confirm" type="password" class="form-control" placeholder="Enter Confirm Password" name="password_confirmation" disabled required>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button class="btn btn-sm btn-primary" type="submit"><i class="fa fa-dot-circle-o"></i> Submit</button>
                <button class="btn btn-sm btn-danger" type="reset"><i class="fa fa-ban"></i> Reset</button>
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