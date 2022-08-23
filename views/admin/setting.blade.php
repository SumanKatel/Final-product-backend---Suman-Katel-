@extends('admin.master')
@section('title', $page_header)
@section('content-header', $page_header)
@section('content')
<div class="card">
	<form class="" enctype="multipart/form-data" method="POST" action="{{ route('update.setting') }}">
		{{ csrf_field() }}
		<div class="card-header">{{ $page_header }}
			<div class="card-header-actions">
				<button type="submit" class="btn btn-success btn-sm">Submit</button>
			</div>
		</div>
		<div class="card-body">
			<div class="row">
				<div class="col">
					<ul class="nav nav-pills mb-1" id="pills-tab" role="tablist">
						<li class="nav-item">
							<a class="nav-link active" id="tab-1" data-toggle="pill" href="#pills-basic" role="tab" aria-controls="pills-basic" aria-selected="true">Basic Information</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" id="tab-2" data-toggle="pill" href="#pills-social" role="tab" aria-controls="pills-social" aria-selected="false">Social</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" id="tab-3" data-toggle="pill" href="#pills-metatags" role="tab" aria-controls="pills-metatags" aria-selected="false">Meta Tags</a>
						</li>
					</ul>
					<div class="tab-content" id="pills-tabContent">
						<div class="tab-pane fade show active" id="pills-basic" role="tabpanel" aria-labelledby="tab-1">
							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label" for="title">Title</label>
										<input type="text" class="form-control" id="title" name="title" value="{{ $settingdata->title }}" >
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label" for="address">Address (English)</label>
										<input type="text" class="form-control" id="address" name="address" value="{{ $settingdata->address }}" >
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label" for="mobile_no">Mobile Number</label>
										<input type="text" class="form-control" id="mobile_no" name="mobile_no" value="{{ $settingdata->mobile_no }}" >
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label" for="phone_no">Phone Number</label>
										<input type="text" class="form-control" id="phone_no" name="phone_no" value="{{ $settingdata->phone_no }}" >
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label" for="fax_no">Fax Number</label>
										<input type="text" class="form-control" id="fax_no" name="fax_no" value="{{ $settingdata->fax_no }}" >
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label" for="email">Email</label>
										<input type="text" class="form-control" id="email" name="email" value="{{ $settingdata->email }}" >
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label" for="checkmail">Check Mail Url</label>
										<input type="text" class="form-control" id="checkmail" name="checkmail" value="{{ $settingdata->checkmail }}" >
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label" for="office_hour">Office Hour</label>
										<input type="text" class="form-control" id="office_hour" name="office_hour" value="{{ $settingdata->office_hour }}" >
									</div>
								</div>

								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label" for="latitude">Latitude</label>
										<input type="text" class="form-control" id="latitude" name="latitude" value="{{ $settingdata->latitude }}" >
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label" for="chairman_name">Chairman Name</label>
										<input type="text" class="form-control" id="chairman_name" name="chairman_name" value="{{ $settingdata->chairman_name }}" >
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label" for="images">Chairman Image</label>
										<input type="file" class="form-control" id="images" name="chairman_image">
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label" for="longitude">Longitude</label>
										<input type="text" class="form-control" id="longitude" name="longitude" value="{{ $settingdata->longitude }}" >
									</div>
								</div>
								
								<div class="col-md-3">
									<div class="form-group">
										<label for="logo">Logo</label>
										@if(!empty($settingdata->logo))
										<img src="<?php echo $settingdata->logo ?>" class="fancybox" id="prev_img" />
										@else
										<img src="{{ asset('admin/images/no-image.png', $secure = null) }}" class="fancybox" id="prev_img" />
										@endif
										<a href="{{ url('/uploads/filemanager/dialog.php?type=1&field_id=logo') }}" data-fancybox-type="iframe" class="btn btn-info fancy">Insert</a>
										<button class="btn btn-danger remove_box_image" type="button">Remove</button>
										<input type="hidden" value="{{ isset($settingdata->logo)?$settingdata->logo:'' }}"  name="logo" class="form-control" id="logo">
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label" for="feature_header">Chairman Message</label>
										<textarea class="form-control" id="chairman_message" rows="8" cols="2" name="chairman_message">{{ $settingdata->chairman_message }}</textarea>
									</div>
								</div>
								
							<!--	<div class="col-md-6">
									<div class="form-group">
										<label class="control-label" for="feature_header">Feature header</label>
										<textarea class="form-control" id="feature_header" rows="8" cols="2" name="feature_header">{{ $settingdata->feature_header }}</textarea>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label" for="product_header">Product Header</label>
										<textarea class="form-control" id="product_header" rows="8" cols="2" name="product_header">{{ $settingdata->product_header }}</textarea>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label" for="client_header">Client Header</label>
										<textarea class="form-control" id="client_header" rows="8" cols="2" name="client_header">{{ $settingdata->client_header }}</textarea>
									</div>
								</div>-->
							
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label" for="map_detail">Footer Content</label>
										<textarea class="form-control" id="map_detail" rows="8" cols="2" name="footer_content">{{ $settingdata->footer_content }}</textarea>
									</div>
								</div>
							</div>
						</div>
						<div class="tab-pane fade" id="pills-social" role="tabpanel" aria-labelledby="tab-2">
							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label" for="facebook">Facebook</label>
										<input type="text" class="form-control" id="facebook" name="facebook" value="{{ $settingdata->facebook }}" >
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label" for="twitter">Twitter</label>
										<input type="text" class="form-control" id="twitter" name="twitter" value="{{ $settingdata->twitter }}" >
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label" for="google_plus">Google Plus</label>
										<input type="text" class="form-control" id="google_plus" name="google_plus" value="{{ $settingdata->google_plus }}" >
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label" for="skype">Skype</label>
										<input type="text" class="form-control" id="skype" name="skype" value="{{ $settingdata->skype }}" >
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label" for="linkedin">Linkedin</label>
										<input type="text" class="form-control" id="linkedin" name="linkedin" value="{{ $settingdata->linkedin }}" >
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label" for="youtube">YouTube</label>
										<input type="text" class="form-control" id="youtube" name="youtube" value="{{ $settingdata->youtube }}" >
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label" for="instagram">Instagram</label>
										<input type="text" class="form-control" id="instagram" name="instagram" value="{{ $settingdata->instagram }}" >
									</div>
								</div>
							</div>
						</div>
						<div class="tab-pane fade" id="pills-metatags" role="tabpanel" aria-labelledby="tab-3">
							<div class="form-group">
								<label class="control-label" for="meta_keywords">Meta Keywords</label>
								<input type="text" class="form-control" id="meta_keywords" name="meta_keywords" value="{{ $settingdata->meta_keywords }}" >
							</div>
							<div class="form-group">
								<label class="control-label" for="meta_descriptions">Meta Descriptions</label>
								<textarea class="form-control" id="meta_descriptions" rows="10" cols="20" name="meta_descriptions">{{ $settingdata->meta_descriptions }}</textarea>
							</div>
						</div>
					</div>
				</div>
			</div>
			<hr>
		</div>
	</form>
</div>
@endsection