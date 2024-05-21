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
            <li class="nav-item">
                <a href="{{ route('pago.buscarCuenta') }}" class="nav-link">
                    <i class="nav-icon fas fa-rss"></i>
                    <p>
                        {{ __('Busqueda de cuentas') }}
                    </p>
                </a>
            </li>
        </ul>
    </nav>
    <!-- /.sidebar-menu -->
</div>
<!-- /.sidebar -->