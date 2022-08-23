<?php
$url = Request::segment(2);
$urlx = Request::url();
$menus = App\Model\admin\AdminMenu::getAllMenus();
?>
<div class="sidebar">
    <nav class="sidebar-nav">
        <ul class="nav">
            <li class="nav-item">
                <a class="nav-link {{ ($urlx == route('dashboard'))?'active':'' }}" href="{{ route('dashboard') }}">
                <i class="nav-icon icon-drop"></i> Dashboard</a>
            </li>
            @if(Session::get('admin')['userid'] == '1')
                <li class="nav-item">
                    <a class="nav-link {{ ($urlx == route('user.list'))?'active':'' }}" href="{{ route('user.list') }}">
                    <i class="nav-icon icon-user"></i> User List</a>
                </li>
            @endif

            @if(count($menus)>0)
            @foreach($menus as $menu)
                @if($menu->parent_id == 0)
                    <?php $secondLevelMenus = App\Model\admin\AdminMenu::getMenu($menu->id); ?>
                    @if(count($secondLevelMenus)>0)
                    <li class="nav-item nav-dropdown <?php
                      foreach($secondLevelMenus as $secondLevelMenu) {
                        $urlxx = route($secondLevelMenu->menu_link);
                        $lasturl = explode('/',$urlxx);
                        echo ($url == end($lasturl))?'active':'';
                      }
                    ?>">
                        <a class="nav-link nav-dropdown-toggle" href="JavaScript:Void(0);">
                        <i class="nav-icon icon-puzzle"></i> {{ $menu->menu_name }}</a>
                        <ul class="nav-dropdown-items">
                            @foreach($secondLevelMenus as $secondLevelMenu)
                            <li class="nav-item <?php
                                $urlxx = route($secondLevelMenu->menu_link);
                                $lasturl = explode('/',$urlxx);
                                echo ($url == end($lasturl))?'open':'';
                                ?>">
                                <a class="nav-link <?php
                                $urlxx = route($secondLevelMenu->menu_link);
                                $lasturl = explode('/',$urlxx);
                                echo ($url == end($lasturl))?'active':'';
                                ?>" href="{{ route($secondLevelMenu->menu_link) }}">
                                <i class="nav-icon icon-puzzle"></i> {{ $secondLevelMenu->menu_name }}</a>
                            </li>
                            @endforeach
                        </ul>
                    </li>
                  @endif
              @endif
          @endforeach
        @endif
        </ul>
    </nav>
    <button class="sidebar-minimizer brand-minimizer" type="button"></button>
</div>