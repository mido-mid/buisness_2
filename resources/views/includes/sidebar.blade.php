<!-- ========== Left Sidebar Start ========== -->
<div class="vertical-menu">

    <div data-simplebar class="h-100">

        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">
                <li class="menu-title">{{__('admin.dashboard')}}</li>
                     <!-- Redeny -->
                      <!-- Admin Panel -->

                <!-- Categories -->
                <li>
                    <a href="{{ route('categories.index') }}">
                        <i class="dripicons-network-1"></i>
                        <span>{{__('admin.categories')}}</span>
                    </a>
                </li>

                <!-- Reports -->
                <li>
                    <a href="{{ route('reports.index') }}">
                        <i class="dripicons-network-1"></i>
                        <span>{{__('admin.reports')}}</span>
                    </a>
                </li>
            </ul>
        </div>
        <!-- Sidebar -->
    </div>
</div>
<!-- Left Sidebar End -->
