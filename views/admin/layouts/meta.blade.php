<head>
<base href="./">
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
<meta name="description" content="CoreUI - Open Source Bootstrap Admin Template">
<meta name="author" content="Łukasz Holeczek">
<meta name="keyword" content="Bootstrap,Admin,Template,Open,Source,jQuery,CSS,HTML,RWD,Dashboard">
<title>|| Admin || @yield('title')</title>
<!-- Icons-->
<link rel="icon" type="image/png" href="{{ asset($sitedetail->shortcut_icon) }}" sizes="any"/>
<link href="{{ asset('admin/vendors/@coreui/icons/css/coreui-icons.min.css') }}" rel="stylesheet">
<link href="{{ asset('admin/vendors/flag-icon-css/css/flag-icon.min.css') }}" rel="stylesheet">
<link href="{{ asset('admin/vendors/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">
<link href="{{ asset('admin/vendors/simple-line-icons/css/simple-line-icons.css') }}" rel="stylesheet">
{{--  --}}
<link rel="stylesheet" type="text/css" href="{{ asset('admin/assets/datatables/jquery.dataTables.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('admin/assets/select2/select2.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('admin/assets/fancybox/jquery.fancybox.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('admin/assets/datepicker/datepicker3.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('admin/assets/jquery-confirm/jquery-confirm.min.css') }}">
{{--  --}}
<!-- Main styles for this application-->
<link href="{{ asset('admin/css/style.css') }}" rel="stylesheet">
<link href="{{ asset('admin/vendors/pace-progress/css/pace.min.css') }}" rel="stylesheet">
<link href="{{ asset('admin/assets/custom_style.css') }}" rel="stylesheet">
@stack('style')
<script src="{{ asset('admin/vendors/jquery/js/jquery.min.js') }}"></script>
<script type="text/javascript">
window.Laravel = {!! json_encode([
'csrfToken' => csrf_token(),
]) !!};
</script>
</head>