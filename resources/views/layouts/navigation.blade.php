<!-- Sidebar -->
<div class="sidebar">
    <!-- Sidebar user panel (optional) -->
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="info">
            <a href="{{ route('profile.show') }}" class="d-block">{{ Auth::user()->name }}</a>
        </div>
    </div>

    <!-- Sidebar Menu -->
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
            data-accordion="false">
            <li class="nav-item">
                <a href="{{ route('home') }}" class="nav-link">
                    <i class="nav-icon fas fa-th"></i>
                    <p>
                        {{ __('Dashboard') }}
                    </p>
                </a>
            </li>

{{--            <li class="nav-item">--}}
{{--                <a href="#" class="nav-link">--}}
{{--                    <i class="nav-icon fas fa-circle nav-icon"></i>--}}
{{--                    <p>--}}
{{--                        Pagos--}}
{{--                        <i class="fas fa-angle-left right"></i>--}}
{{--                    </p>--}}
{{--                </a>--}}
{{--                <ul class="nav nav-treeview" style="display: none;">--}}
{{--                    <li class="nav-item">--}}
{{--                        <a href="{{ route('pagos.index') }}" class="nav-link">--}}
{{--                            <i class="far fa-circle nav-icon"></i>--}}
{{--                            <p>Relación</p>--}}
{{--                        </a>--}}
{{--                    </li>--}}
{{--                </ul>--}}
{{--            </li>--}}

{{--            <li class="nav-item">--}}
{{--                <a href="#" class="nav-link">--}}
{{--                    <i class="nav-icon fas fa-circle nav-icon"></i>--}}
{{--                    <p>--}}
{{--                        Cobradores--}}
{{--                        <i class="fas fa-angle-left right"></i>--}}
{{--                    </p>--}}
{{--                </a>--}}
{{--                <ul class="nav nav-treeview" style="display: none;">--}}
{{--                    <li class="nav-item">--}}
{{--                        <a href="{{ route('cobrador.index') }}" class="nav-link">--}}
{{--                            <i class="far fa-circle nav-icon"></i>--}}
{{--                            <p>Relación</p>--}}
{{--                        </a>--}}
{{--                    </li>--}}
{{--                    <li class="nav-item">--}}
{{--                        <a href="{{ route('cobrador.alta') }}" class="nav-link">--}}
{{--                            <i class="far fa-circle nav-icon"></i>--}}
{{--                            <p>Alta</p>--}}
{{--                        </a>--}}
{{--                    </li>--}}
{{--                </ul>--}}
{{--            </li>--}}


            <li class="nav-item">
                <a href="{{ route('users.index') }}" class="nav-link">
                    <i class="nav-icon fas fa-user"></i>
                    <p>
                        {{ __('Administradores') }}
                    </p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('cobrador.index') }}" class="nav-link">
                    <i class="nav-icon fas fa-users"></i>
                    <p>
                        {{ __('Cobradores') }}
                    </p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('pagos.index') }}" class="nav-link">
                    <i class="nav-icon fas fa-credit-card"></i>
                    <p>
                        {{ __('Pagos') }}
                    </p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('archivos.index') }}" class="nav-link">
                    <i class="nav-icon fas fa-rss"></i>
                    <p>
                        {{ __('Archivos excel') }}
                    </p>
                </a>
            </li>
{{--            <li class="nav-item">--}}
{{--                <a methods="post" href="{{ route('pagos.trerCobros',[45449]) }}" class="nav-link">--}}
{{--                    <i class="nav-icon fas fa-users"></i>--}}
{{--                    <p>--}}
{{--                        {{ __('temporal') }}--}}
{{--                    </p>--}}
{{--                </a>--}}
{{--            </li>--}}
{{--            <li class="nav-item">--}}
{{--                <a href="{{ route('about') }}" class="nav-link">--}}
{{--                    <i class="nav-icon far fa-address-card"></i>--}}
{{--                    <p>--}}
{{--                        {{ __('About us') }}--}}
{{--                    </p>--}}
{{--                </a>--}}
{{--            </li>--}}

{{--            <li class="nav-item">--}}
{{--                <a href="#" class="nav-link">--}}
{{--                    <i class="nav-icon fas fa-circle nav-icon"></i>--}}
{{--                    <p>--}}
{{--                        Two-level menu--}}
{{--                        <i class="fas fa-angle-left right"></i>--}}
{{--                    </p>--}}
{{--                </a>--}}
{{--                <ul class="nav nav-treeview" style="display: none;">--}}
{{--                    <li class="nav-item">--}}
{{--                        <a href="#" class="nav-link">--}}
{{--                            <i class="far fa-circle nav-icon"></i>--}}
{{--                            <p>Child menu</p>--}}
{{--                        </a>--}}
{{--                    </li>--}}
{{--                </ul>--}}
{{--            </li>--}}
        </ul>
    </nav>
    <!-- /.sidebar-menu -->
</div>
<!-- /.sidebar -->