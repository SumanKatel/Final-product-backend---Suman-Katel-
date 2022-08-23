<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
        <title>|| Admin Login ||</title>
        <link rel="icon" type="image/png" href="{{ asset($sitedetail->shortcut_icon) }}" sizes="any"/>

        <!-- Icons-->
        <link href="{{ asset('admin/vendors/@coreui/icons/css/coreui-icons.min.css') }}" rel="stylesheet">
        <link href="{{ asset('admin/vendors/flag-icon-css/css/flag-icon.min.css') }}" rel="stylesheet">
        <link href="{{ asset('admin/vendors/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">
        <link href="{{ asset('admin/vendors/simple-line-icons/css/simple-line-icons.css') }}" rel="stylesheet">
        <!-- Main styles for this application-->
        <link href="{{ asset('admin/css/style.css') }}" rel="stylesheet">
        {{-- <link href="{{ asset('admin/vendors/pace-progress/css/pace.min.css') }}" rel="stylesheet"> --}}
    </head>
    <body class="app flex-row align-items-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card-group">
                        <div class="card p-4">
                            <div class="card-body">
                                <h1>Log In</h1>
                                <p class="text-muted">Sign In to your account</p>
                                @if (Session::has('message'))
                                <div class="alert alert-danger alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    {{ Session::get('message') }}
                                </div>
                                @endif
                                <form action="{{ route('logincheck') }}" method="POST">
                                    {{ csrf_field() }}
                                    <div class="input-wrap">
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="icon-user"></i>
                                                </span>
                                            </div>
                                            <input class="form-control"  name="username" type="text" placeholder="Username" value="{{ old('username')}}">
                                            
                                        </div>
                                        @if ($errors->has('username'))
                                        <p class="text-danger">{{ $errors->first('username') }}</p>
                                        @endif
                                        
                                    </div>
                                    <div class="input-wrap">
                                        <div class="input-group mb-4">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="icon-lock"></i>
                                                </span>
                                            </div>
                                            <input type="password" class="form-control" name="password" placeholder="Password">
                                        </div>
                                        @if ($errors->has('password'))
                                        <p class="text-danger">{{ $errors->first('password') }}</p>
                                        @endif
                                    </div>
                                    <div class="row">
                                        <div class="col-6">
                                            <button class="btn btn-primary px-4" type="submit">Login</button>
                                        </div>
                                        <div class="col-6 text-right">
                                            <button class="btn btn-link px-0" type="button">Forgot password?</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="card text-white bg-primary py-5 d-md-down-none" style="width:44%">
                            <div class="card-body text-center">
                                <div>
                                    @if($sitedetail->title)
                                    <p>{{ $sitedetail->title}}</p>
                                    @else
                                    <p>Site Name</p>
                                    @endif
                                    @if($sitedetail->logo)
                                    <img src="{{ asset($sitedetail->logo) }}" style="max-width: 100%; height: 150px;">
                                    @else
                                    <img src="{{ asset('uploads/default/default-website.png') }}" style="max-width: 100%; height: 150px;">
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- CoreUI and necessary plugins-->
        <script src="{{ asset('admin/vendors/jquery/js/jquery.min.js') }}"></script>
    </body>
</html>