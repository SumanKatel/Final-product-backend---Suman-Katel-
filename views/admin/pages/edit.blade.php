@extends('admin.master')
@section('title', $page_header)
@section('content-header', $page_header)
@section('content')
<div class="card">
    <div class="card-header">{{ $page_header }}
        <div class="card-header-actions">
            <a class="card-header-action btn btn-warning" href="{{ route($link.'.index') }}">
                <small class="text-muted">{!! VIEWLIST_ICON !!}</small>
            </a>
        </div>
    </div>
    <div class="card-body">
        <form class="" method="POST" action="{{ route($link.'.update', $record->id) }} ">
            {{ csrf_field() }}
            {{ method_field('PATCH') }}
            <div class="row">
            <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
                <div class="form-group">
                    <label class="control-label" for="title">Permalink</label>
                    <input type="text" class="form-control" id="slug" name="slug" value="{{ $record->slug }}" >
                    @if ($errors->has('slug'))
                        <span class="help-block">
                            <strong>{{ $errors->first('slug') }}</strong>
                        </span>
                    @endif
                    {{-- <p><a href="{{ route('page.detail',$record->slug) }}" target="_blank" class="pull-right"> <u>View Url</u></a></p> --}}
                </div>
                <div class="form-group">
                    <label class="control-label" for="title">Title</label>
                    <input type="text" class="form-control" id="title" name="title" value="{{ $record->title }}" >
                    @if ($errors->has('title'))
                        <span class="help-block">
                            <strong>{{ $errors->first('title') }}</strong>
                        </span>
                    @endif
                </div>
                <div class="form-group">
                    <label class="control-label" for="description">Description : </label>
                    <br>
                    <textarea id="my-editor" class="tinymce" name="description" placeholder="Place some text here" >{{ $record->description }}</textarea>
                    @if ($errors->has('description'))
                        <span class="help-block">
                            <strong>{{ $errors->first('description') }}</strong>
                        </span>
                    @endif
                </div>               
                <div class="form-group">
                    <label class="control-label" for="meta_title">SEO Title </label>
                    <input type="text" class="form-control" id="meta_title" name="meta_title" value="{{ $record->meta_title }}" >
                </div>
                <div class="form-group">
                    <label class="control-label" for="meta_keywords">SEO Keywords </label>
                    <input type="text" class="form-control" id="meta_keywords" name="meta_keywords" value="{{ $record->meta_keywords }}" >
                </div>
                <div class="form-group">
                    <label class="control-label" for="meta_description">SEO Description </label>
                    <input type="text" class="form-control" id="meta_description" name="meta_description" value="{{ $record->meta_description }}" >
                </div>
            </div>

            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                <div class="form-group">
                   <label class="control-label">Featured Image</label>
                    <?php if (!empty($record->image)) { ?>
                        <img src="<?php echo asset($record->image, $secure = null) ?>" class="fancybox" alt="" title="" id="prev_img" />
                    <?php } else { ?>
                        <img src="{{ asset('admin/images/no-image.png', $secure = null) }}" class="fancybox" alt="" title="" id="prev_img" />
                    <?php } ?>
                    <div class="clearfix"></div>
                    <a href="<?php echo url('/uploads/filemanager/dialog.php?type=1&field_id=image') ?>" data-fancybox-type="iframe" class="btn btn-info fancy">Insert</a>
                    <button class="btn btn-danger remove_box_image" type="button">Remove</button>
                    <button class="btn btn-warning prev_box_image" type="button" style="display: none;">Previous Image</button>
                    <input type="hidden" value="<?php echo isset($record->image)?asset($record->image):'' ?>"  name="image" class="form-control" id="image">
                </div>
                <div class="form-group">
                    <label for="status">Published Date</label>
                    <input type="text" name="published_date" class="form-control datepicker" value="{{ $record->published_date }}">
                </div>
                <div class="form-group">
                    <label for="status">Status</label>
                    <select name="status" id="statusid" class="form-control">
                        <option value="1"  @if($record->status == '1'){{ 'selected' }} @endif >{!! PUBLISH !!}</option>
                        <option value="0"  @if($record->status == '0'){{ 'selected' }} @endif >{!! UNPUBLISH !!}</option>
                    </select>
                </div>
               <div class="form-group">
                    <button type="submit" class="btn btn-success">Update</button>
                    <button type="reset" class="btn btn-danger resetbtn">Clear</button>
                </div>
            </div>
        </div>
        </form>
    </div>
</div>
@endsection