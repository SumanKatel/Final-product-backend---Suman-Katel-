<!DOCTYPE html>
<html lang="en">
    @include('admin.layouts.meta')
    <body class="app header-fixed sidebar-fixed aside-menu-fixed sidebar-lg-show">
        @include('admin.layouts.header')
        <div class="app-body">
            @include('admin.layouts.menu')
            <main class="main mbottom10">
                <div class="container-fluid">
                    <div class="animated fadeIn">
                        <div class="row">
                            <div class="col-md-12">
                                @if (Session::has('message'))
                                <div class="alert alert-danger alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    {{ Session::get('message') }}
                                </div>
                                @endif
                                @if (Session::has('success_message'))
                                <div class="alert alert-success alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    {{ Session::get('success_message') }}
                                </div>
                                @endif
                            </div>
                        </div>
                        @section('content')
                        @show
                    </div>
                </div>
            </main>
        </div>
        @include('admin.layouts.footer')
        @include('admin.layouts.script')
    </body>
</html>