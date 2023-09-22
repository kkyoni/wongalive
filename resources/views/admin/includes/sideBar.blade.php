
<nav class="navbar-default navbar-static-side" role="navigation">
    <div class="sidebar-collapse">
        <ul class="nav metismenu" id="side-menu">
            <li class="nav-header">
                <div class="dropdown profile-element">
                    <img alt="image" class="rounded-circle" src="{{ url(\Settings::get('application_logo')) }}"  height="60px" width="60px" style="border-radius:20%!important"/>
                    <ul class="dropdown-menu animated fadeInLeft m-t-xs">
                        <li><a class="dropdown-item" href="{{ route('admin.profile') }}">Profile</a></li>
                        <li class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="{{ route('admin.logout') }}">Logout</a></li>
                    </ul>
                </div>
                <div class="logo-element">
                    <img alt="image" class="rounded-circle" src="{{ url(\Settings::get('favicon_logo')) }}"  height="60px" width="60px" style="border-radius:20%!important"/>
                </div>
            </li>
            <li class="@if(Request::segment('2') == 'dashboard') active @endif">
                <a href="{{ route('admin.dashboard') }}">
                    <i class="fa fa-home"></i>
                    <span class="nav-label">
                        Dashboard
                    </span>
                </a>
            </li>
            <li class="@if(Request::segment('2') == 'user') active @endif">
                <a href="{{ route('admin.index') }}">
                    <i class="fa fa-users"></i>
                    <span class="nav-label">Users </span>
                </a>
            </li>

            <!-- <li class="@if(Request::segment('2') == 'user') active @endif">
                <a href="{{ route('admin.videos.index') }}">
                    <i class="fa fa-video-camera"></i>
                    <span class="nav-label">Videos </span>
                </a>
            </li> -->

          <li class=" @if(Request::segment('2') == 'diamond') active @endif">
                <a href="{{ route('admin.diamond.index') }}">
                    <i class="fa fa-diamond"></i>
                    <span class="nav-label">
                        Diamonds
                    </span>
                    </a>
             </li>
           
          <li class=" @if(Request::segment('2') == 'banner') active @endif">
                <a href="{{ route('admin.banner.index') }}">
                    <i class="fa fa-picture-o"></i>
                    <span class="nav-label">
                        Banners
                    </span>
                    </a>
             </li>
           
            <li class=" @if(Request::segment('2') == 'transactions') active @endif">
                <a href="{{ route('admin.transactions.index') }}">
                    <i class="fa fa-money"></i>
                    <span class="nav-label">
                        Transactions
                    </span>
                </a>
            </li>

            <li class=" @if(Request::segment('2') == 'report_category') active @endif">
                <a href="{{ route('admin.report_category.index') }}">
                    <i class="fa fa-universal-access"></i>
                    <span class="nav-label">
                        Report Category
                    </span>
                </a>
            </li>

            <li class=" @if(Request::segment('2') == 'report_user') active @endif">
                <a href="{{ route('admin.report_user.index') }}">
                    <i class="fa fa-ban"></i>
                    <span class="nav-label">
                        Report User
                    </span>
                </a>
            </li>

            <li class=" @if(Request::segment('2') == 'gift') active @endif">
                <a href="{{ route('admin.gift.index') }}">
                    <i class="fa fa-gift"></i>
                    <span class="nav-label">
                        Gift Management
                    </span>
                </a>
            </li>

            <li class=" @if(Request::segment('2') == 'conversations') active @endif">
                <a href="{{ route('admin.conversations.index') }}">
                    <i class="fa fa-comments"></i>
                    <span class="nav-label">
                        Conversations Pages
                    </span>
                </a>
            </li>

            <li class=" @if(Request::segment('2') == 'cms') active @endif">
                <a href="{{ route('admin.cms.index') }}">
                    <i class="fa fa-edit"></i>
                    <span class="nav-label">
                        CMS Pages
                    </span>
                </a>
            </li>
           
            <li class="@if(Request::segment('2') == 'settings') active @endif">
                <a href="{{ url('admin/settings') }}">
                    <i class="fa fa-cogs"></i>
                    <span class="nav-label">
                        Site Settings
                    </span>
                </a>
            </li>
        </ul>
    </div>
</nav>