{{-- resources/views/auth/login.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Iniciar Sesión - {{ config('app.name', 'Sistema de Convenios') }}</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/icheck-bootstrap/3.0.1/icheck-bootstrap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/css/adminlte.min.css">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        
        .login-box {
            margin: 0 auto;
        }
        
        .card {
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            border: none;
        }
        
        .card-header {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            text-align: center;
            padding: 2rem 1rem 1rem;
        }
        
        .card-body {
            padding: 2rem;
            background: white;
            border-radius: 0 0 15px 15px;
        }
        
        .login-logo {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: white;
        }
        
        .login-title {
            font-size: 1.8rem;
            font-weight: 300;
            margin: 0;
        }
        
        .login-subtitle {
            font-size: 0.9rem;
            opacity: 0.8;
            margin-top: 0.5rem;
        }
        
        .form-control {
            border-radius: 10px;
            padding: 0.75rem 1rem;
            border: 1px solid #ddd;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
        
        .input-group-text {
            border-radius: 10px 0 0 10px;
            background-color: #f8f9fa;
            border-color: #ddd;
        }
        
        .input-group .form-control {
            border-radius: 0 10px 10px 0;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            border: none;
            border-radius: 10px;
            padding: 0.75rem 2rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 123, 255, 0.4);
        }
        
        .alert {
            border-radius: 10px;
            border: none;
        }
        
        .remember-me {
            margin: 1rem 0;
        }
        
        .login-footer {
            text-align: center;
            margin-top: 2rem;
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        .system-info {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 1rem;
            margin-top: 2rem;
            color: white;
            text-align: center;
        }
        
        .loading {
            display: none;
        }
        
        @media (max-width: 576px) {
            .login-box {
                margin: 1rem;
                width: calc(100% - 2rem);
            }
            
            .card-body {
                padding: 1.5rem;
            }
        }
    </style>
</head>

<body class="hold-transition login-page">
    <div class="login-box">
        <!-- Logo y título -->
        <div class="card">
            <div class="card-header">
                <div class="login-logo">
                    <i class="fas fa-handshake fa-3x"></i>
                </div>
                <h1 class="login-title">Sistema de Convenios</h1>
                <p class="login-subtitle">Ingrese sus credenciales para acceder</p>
            </div>
            
            <div class="card-body">
                <!-- Mensajes de error/éxito -->
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle mr-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                <!-- Formulario de login -->
                <form id="loginForm" method="POST" action="{{ route('login') }}">
                    @csrf
                    
                    <!-- Campo Usuario/Email -->
                    <div class="mb-3">
                        <label for="login" class="form-label">
                            <i class="fas fa-user mr-2"></i>Usuario o Email
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-user"></i>
                            </span>
                            <input type="text" 
                                   class="form-control @error('login') is-invalid @enderror" 
                                   id="login" 
                                   name="login" 
                                   value="{{ old('login') }}" 
                                   placeholder="Ingrese su usuario o email"
                                   required
                                   autofocus>
                        </div>
                        @error('login')
                        <div class="invalid-feedback d-block">
                            <i class="fas fa-exclamation-triangle mr-1"></i>{{ $message }}
                        </div>
                        @enderror
                    </div>

                    <!-- Campo Contraseña -->
                    <div class="mb-3">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock mr-2"></i>Contraseña
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   id="password" 
                                   name="password" 
                                   placeholder="Ingrese su contraseña"
                                   required>
                            <span class="input-group-text" id="togglePassword" style="cursor: pointer;">
                                <i class="fas fa-eye" id="eyeIcon"></i>
                            </span>
                        </div>
                        @error('password')
                        <div class="invalid-feedback d-block">
                            <i class="fas fa-exclamation-triangle mr-1"></i>{{ $message }}
                        </div>
                        @enderror
                    </div>

                    <!-- Recordarme -->
                    <div class="remember-me">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">
                                Recordar mi sesión
                            </label>
                        </div>
                    </div>

                    <!-- Botón de login -->
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg" id="loginButton">
                            <span class="login-text">
                                <i class="fas fa-sign-in-alt mr-2"></i>Iniciar Sesión
                            </span>
                            <span class="loading">
                                <i class="fas fa-spinner fa-spin mr-2"></i>Iniciando sesión...
                            </span>
                        </button>
                    </div>
                </form>

                <!-- Enlaces adicionales -->
                <div class="login-footer">
                    <div class="row">
                        <div class="col-12">
                            <a href="#" class="text-muted">
                                
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información del sistema -->
        <div class="system-info">
            <div class="row text-center">
                <div class="col-12">
                    <small>
                        <i class="fas fa-shield-alt mr-1"></i>
                        Sistema seguro con auditoría completa
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function() {
            // Toggle password visibility
            $('#togglePassword').click(function() {
                var passwordField = $('#password');
                var eyeIcon = $('#eyeIcon');
                
                if (passwordField.attr('type') === 'password') {
                    passwordField.attr('type', 'text');
                    eyeIcon.removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    passwordField.attr('type', 'password');
                    eyeIcon.removeClass('fa-eye-slash').addClass('fa-eye');
                }
            });

            // Form submission with loading state
            $('#loginForm').submit(function() {
                $('#loginButton').prop('disabled', true);
                $('.login-text').hide();
                $('.loading').show();
            });

            // Auto hide alerts
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 5000);

            // Prevent multiple form submissions
            var submitted = false;
            $('#loginForm').submit(function(e) {
                if (submitted) {
                    e.preventDefault();
                    return false;
                }
                submitted = true;
            });

            // Focus on first input with error
            if ($('.is-invalid').length > 0) {
                $('.is-invalid').first().focus();
            }
        });
    </script>
</body>
</html>