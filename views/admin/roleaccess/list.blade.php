@extends('admin.master')
@section('title', $page_header)
@section('content-header', $page_header)
@section('content')
<div class="card">
    <div class="card-body">
        <form method="GET" action="{{ route('role-access.index') }}" class="form-horizontal">
                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                    <div class="form-group">
                        <label for="group">Select Group Name</label>
                        <select name="group_id" id="group_id" class="form-control">
                            @foreach($grouplist as $grp)
                                <option value="{{ $grp->id }}" <?php echo (isset($_REQUEST['group_id'])&& $grp->id == $_REQUEST['group_id'])?'selected':''; ?> >{{ $grp->title }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                    <div class="form-group">
                        <button type="submit" class="btn btn-success">Filter</button>
                        <button type="reset" class="btn btn-danger">Reset</button>
                    </div>
                </div>
            </form>
            <div class="clearfix"></div>
            @if(!empty($list))
                <div class="table-responsive">
                    <table class="table  table-striped table-hover">
                        <thead>
                        <tr>
                            <th>Module</th>
                            <th>Read?</th>
                            <th>Write?</th>
                            <th>Edit?</th>
                            <th>Delete?</th>
                        </tr>
                        </thead>
                        <tbody>
                            @if(!empty($list))
                                <?php $i = 1; ?>
                                @foreach($list as $menu)
                                    <tr>
                                        <td>{{ $i }}. {{ $menu->menu_name }}</td>
                                        <td>
                                            <div class="checkbox">
                                                  <label class="switch">
                                                    <input type="checkbox" class="read" {{ ($menu->allow_view == 1)?'checked':null }} value="{{$menu->group_role_id}}">
                                                    <span class="slider round"></span>
                                                  </label>
                                            </div>
                                        </td>
                                        <td><!-- Rounded switch -->
                                            <div class="checkbox">
                                              <label class="switch">
                                                <input type="checkbox" class="write" {{ ($menu->allow_add == 1)?'checked':null }} value="{{$menu->group_role_id}}">
                                                <span class="slider round"></span>
                                              </label>
                                            </div>
                                        </td>
                                        <td><!-- Rounded switch -->
                                          <div class="checkbox">
                                              <label class="switch">
                                                <input type="checkbox" class="edit" {{ ($menu->allow_edit == 1)?'checked':null }} value="{{$menu->group_role_id}}">
                                                <span class="slider round"></span>
                                              </label>
                                            </div>
                                        </td>
                                        <td><!-- Rounded switch -->
                                          <div class="checkbox">
                                              <label class="switch">
                                                <input type="checkbox" class="delete" {{ ($menu->allow_delete == 1)?'checked':null }} value="{{$menu->group_role_id}}">
                                                <span class="slider round"></span>
                                              </label>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php
                                        $secondLevelMenus = App\Model\admin\AdminRoleAccess::getAccessMenu($menu->id,Request::get('group_id'));
                                        $j = 1;
                                    ?>
                                    @if(!empty($secondLevelMenus))
                                        @foreach($secondLevelMenus as $secondLevelMenu)
                                            <tr>
                                                <td><p style="padding-left: 15px;">{{ $i.'.'.$j++ }}
                                                        . {{ $secondLevelMenu->menu_name }}</p></td>
                                                <td>
                                                  <div class="checkbox">
                                                      <label class="switch">
                                                        <input type="checkbox" class="read" {{ ($secondLevelMenu->allow_view  == 1)?'checked':null }} value="{{ $secondLevelMenu->group_role_id }}">
                                                        <span class="slider round"></span>
                                                      </label>
                                                    </div>
                                                </td>
                                                <td><!-- Rounded switch -->
                                                  <div class="checkbox">
                                                    <label class="switch">
                                                      <input type="checkbox" class="write" {{ ($secondLevelMenu->allow_add  == 1)?'checked':null }} value="{{ $secondLevelMenu->group_role_id }}">
                                                      <span class="slider round"></span>
                                                    </label>
                                                  </div>
                                                </td>
                                                <td><!-- Rounded switch -->
                                                  <div class="checkbox">
                                                    <label class="switch">
                                                      <input type="checkbox" class="edit" {{ ($secondLevelMenu->allow_edit  == 1)?'checked':null }} value="{{ $secondLevelMenu->group_role_id }}">
                                                      <span class="slider round"></span>
                                                    </label>
                                                  </div>
                                                </td>
                                                <td><!-- Rounded switch -->
                                                  <div class="checkbox">
                                                    <label class="switch">
                                                      <input type="checkbox" class="delete" {{ ($secondLevelMenu->allow_delete  == 1)?'checked':null }} value="{{ $secondLevelMenu->group_role_id }}">
                                                      <span class="slider round"></span>
                                                    </label>
                                                  </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                    <?php $i++; ?>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            @else
                <div class="callout callout-info">
                    Please select the group name from above drop down menu.
                </div>
            @endif
    </div>
</div>
@endsection