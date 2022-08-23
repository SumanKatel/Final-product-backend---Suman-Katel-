<footer class="app-footer">
  <div>
    <span>Copyrights, &copy; {{ date('Y') }} {{ $sitedetail->title }}. </span>
  </div>
  <div class="ml-auto">
    <span>This page took {{ number_format((microtime(true) - LARAVEL_START), 3) }} seconds to render.</span>
    <a href="#">{{ $sitedetail->email }}</a>
  </div>
</footer>
<input type="hidden" name="baseurl" id="baseurl" value="{{ url('/') }}">
<input type="hidden" name="external_filemanager_path" id="external_filemanager_path" value="{{ asset('uploads/filemanager') }}/">
<input type="hidden" name="external_plugins" id="external_plugins" value="{{ asset('uploads/filemanager/plugin.min.js') }}">