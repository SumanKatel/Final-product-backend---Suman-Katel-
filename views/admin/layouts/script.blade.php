
<!-- CoreUI and necessary plugins-->
<script src="{{ asset('admin/vendors/popper.js/js/popper.min.js') }}"></script>
<script src="{{ asset('admin/vendors/bootstrap/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('admin/vendors/pace-progress/js/pace.min.js') }}"></script>
<script src="{{ asset('admin/vendors/perfect-scrollbar/js/perfect-scrollbar.min.js') }}"></script>
<script src="{{ asset('admin/vendors/@coreui/coreui/js/coreui.min.js') }}"></script>
{{--  --}}
<script type="text/javascript" src="{{ asset('admin/assets/tinymce/tinymce.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('admin/assets/datepicker/bootstrap-datepicker.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('admin/assets/fancybox/jquery.fancybox.js') }}"></script>
<script type="text/javascript" src="{{ asset('admin/assets/datatables/jquery.dataTables.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('admin/assets/select2/select2.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('admin/assets/jquery.tablednd_0_5.js') }}"></script>
<script type="text/javascript" src="{{ asset('admin/assets/jquery-confirm/jquery-confirm.min.js') }}"></script>
{{--  --}}
<!-- Plugins and scripts required by this view-->
{{-- <script src="{{ asset('admin/vendors/chart.js/js/Chart.min.js') }}"></script> --}}
{{-- <script src="{{ asset('admin/vendors/@coreui/coreui-plugin-chartjs-custom-tooltips/js/custom-tooltips.min.js') }}"></script> --}}
{{-- <script src="{{ asset('admin/js/main.js') }}"></script> --}}
@stack('script')
<script type="text/javascript" src="{{ asset('admin/assets/admin_scripts.js') }}"></script>