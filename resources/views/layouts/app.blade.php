{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - {{ config('app.name', 'Sistema de Convenios - UNI') }}</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/css/adminlte.min.css">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.0.0/sweetalert2.min.css">
    
    @stack('styles')

    <style>
        .content-wrapper {
            background-color:rgb(255, 255, 255);
        }
        .main-sidebar {
            background: linear-gradient(180deg,rgb(70, 1, 1) 0%,rgb(34, 0, 0) 100%);
        }
        .nav-sidebar .nav-link {
            color: rgba(61, 0, 0, 0.68);
        }
        .nav-sidebar .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
        }
        .nav-sidebar .nav-link.active {
            background-color: rgba(255, 255, 255, 0.2) !important;
            color: white !important;
        }
        .brand-link {
            background-color: rgba(0, 0, 0, 0.1);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 1.5rem 0.5rem;
            text-decoration: none;
        }
        .brand-link:hover {
            text-decoration: none;
        }
        .brand-icon {
            color: white;
            font-size: 3rem;
            margin-bottom: 0.5rem;
            transition: transform 0.3s ease;
        }
        .brand-icon:hover {
            transform: scale(1.1);
            cursor: pointer;
        }
        .brand-text {
            white-space: normal !important; /* permite el salto de línea */
            color: white;
            font-size: 25px;
            font-weight: 300;
            text-align: center;
            line-height: 1.2;
            display: block;                 /* para que respete el ancho del sidebar */
            padding: 0 5px;                 /* opcional: espacio interno para que no pegue al borde */
            margin: 0;
            text-decoration: none;
            word-break: break-word;         /* si hay palabras muy largas, que corten */
            pointer-events: none; /* Evita que el texto sea clickeable */
        }
        .user-panel .info a {
            color: white;
        }
        .main-header.navbar {
            background-color:rgb(255, 255, 255) !important; 
            color: #fff !important;
        }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-light">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                        <i class="fas fa-bars"></i>
                    </a>
                </li>
            </ul>

            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">
                               
                <!-- User Menu -->
                <li class="nav-item dropdown">
                    <a class="nav-link" href="#">
                        <i class="far fa-user"></i>
                        {{ Auth::user()->nombre }}
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <div class="brand-link">
                <a href="{{ route('dashboard') }}" class="brand-icon">
                    <i class="fas fa-handshake fa-3x"></i>
                </a>
                <span class="brand-text">
                    Sistema de Gestión de Convenios
                </span>
            </div>

            <!-- Sidebar -->
            <div class="sidebar">

                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                        
                        <!-- Dashboard -->
                        <li class="nav-item">
                            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Inicio</p>
                            </a>
                        </li>

                        @if(Auth::user()->tienePermiso('convenios.leer'))
                        <!-- Convenios -->
                        <li class="nav-item {{ request()->is('convenios*') ? 'menu-open' : '' }}">
                            <a href="#" class="nav-link {{ request()->is('convenios*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-handshake"></i>
                                <p>
                                    Convenios
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('convenios.index') }}" class="nav-link {{ request()->routeIs('convenios.index') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Listar Convenios</p>
                                    </a>
                                </li>
                                @if(Auth::user()->tienePermiso('convenios.crear'))
                                <li class="nav-item">
                                    <a href="{{ route('convenios.create') }}" class="nav-link {{ request()->routeIs('convenios.create') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Nuevo Convenio</p>
                                    </a>
                                </li>
                                @endif
                            </ul>
                        </li>
                        @endif

                        @if(Auth::user()->tienePermiso('usuarios.leer'))
                        <!-- Gestión de Usuarios -->
                        <li class="nav-item {{ request()->is('usuarios*') ? 'menu-open' : '' }}">
                            <a href="#" class="nav-link {{ request()->is('usuarios*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-users"></i>
                                <p>
                                    Usuarios
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('usuarios.index') }}" class="nav-link {{ request()->routeIs('usuarios.index') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Listar Usuarios</p>
                                    </a>
                                </li>
                                @if(Auth::user()->tienePermiso('usuarios.crear'))
                                <li class="nav-item">
                                    <a href="{{ route('usuarios.create') }}" class="nav-link {{ request()->routeIs('usuarios.create') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Nuevo Usuario</p>
                                    </a>
                                </li>
                                @endif
                            </ul>
                        </li>
                        @endif

                        @if(Auth::user()->tieneRol('super_admin'))
                        <!-- Gestión de Accesos -->
                        <li class="nav-item {{ request()->is('roles*') || request()->is('permisos*') ? 'menu-open' : '' }}">
                            <a href="#" class="nav-link {{ request()->is('roles*') || request()->is('permisos*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-shield-alt"></i>
                                <p>
                                    Gestión de Accesos
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <!-- Roles -->
                                <li class="nav-item">
                                    <a href="{{ route('roles.index') }}" class="nav-link {{ request()->routeIs('roles.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Gestionar Roles</p>
                                    </a>
                                </li>
                                <!-- Permisos -->
                                <li class="nav-item">
                                    <a href="{{ route('permisos.index') }}" class="nav-link {{ request()->routeIs('permisos.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Gestionar Permisos</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        @endif

                        @if(Auth::user()->tienePermiso('reportes.ver'))
                        <!-- Reportes -->
                        <li class="nav-item {{ request()->is('reportes*') ? 'menu-open' : '' }}">
                            <a href="#" class="nav-link {{ request()->is('reportes*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-chart-bar"></i>
                                <p>
                                    Reportes
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('reportes.convenios') }}" class="nav-link {{ request()->routeIs('reportes.convenios') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Reporte de Convenios</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('reportes.usuarios') }}" class="nav-link {{ request()->routeIs('reportes.usuarios') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Reporte de Usuarios</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        @endif

                        @if(Auth::user()->tienePermiso('auditoria.ver'))
                        <!-- Auditoría -->
                        <li class="nav-item">
                            <a href="{{ route('auditoria.index') }}" class="nav-link {{ request()->routeIs('auditoria.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-history"></i>
                                <p>Auditoría</p>
                            </a>
                        </li>
                        @endif

                    </ul>
                </nav>
            </div>
        </aside>

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">@yield('page-title', 'Dashboard')</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                @yield('breadcrumbs')
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    
                    <!-- Mensajes de alerta -->
                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle mr-2"></i>
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert">
                            <span>&times;</span>
                        </button>
                    </div>
                    @endif

                    @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert">
                            <span>&times;</span>
                        </button>
                    </div>
                    @endif

                    @if(session('warning'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        {{ session('warning') }}
                        <button type="button" class="close" data-dismiss="alert">
                            <span>&times;</span>
                        </button>
                    </div>
                    @endif

                    @if(session('info'))
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <i class="fas fa-info-circle mr-2"></i>
                        {{ session('info') }}
                        <button type="button" class="close" data-dismiss="alert">
                            <span>&times;</span>
                        </button>
                    </div>
                    @endif

                    <!-- Contenido principal -->
                    @yield('content')
                    
                </div>
            </section>
        </div>

        <!-- Footer -->
        <footer class="main-footer">
            <strong>Copyright &copy; 2025 Sistema de Gestión de Convenios - UNI.</strong>
            Todos los derechos reservados.
            <div class="float-right d-none d-sm-inline-block">
                <b>Versión</b> 1.0.0
            </div>
        </footer>
    </div>

    <!-- jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <!-- Bootstrap 5 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/js/adminlte.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.0.0/sweetalert2.min.js"></script>

    <script>
        // Configurar token CSRF para AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Auto-ocultar alertas después de 5 segundos
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);

        // Confirmación para eliminaciones
        $(document).on('click', '.btn-delete', function(e) {
            e.preventDefault();
            var form = $(this).closest('form');
            
            Swal.fire({
                title: '¿Está seguro?',
                text: "Esta acción no se puede deshacer",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    </script>

    @stack('scripts')

</body>
</html>