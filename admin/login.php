<?php
require_once '../config.php';

function loginRedirectTargetByRole(string $role): string
{
    return rtrim(BASE_URL, '/') . '/admin/' . ($role === 'admin' ? 'index.php' : 'operator_dashboard.php');
}

// Jika sudah login, redirect ke dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: " . loginRedirectTargetByRole((string) ($_SESSION['role'] ?? 'operator')));
    exit;
}

$error = '';
$success = '';

// Check for logged out message
if (isset($_GET['logged_out'])) {
    $success = 'Anda telah berhasil logout.';
}

// Check for timeout
if (isset($_GET['timeout'])) {
    $error = 'Sesi Anda telah berakhir. Silakan login kembali.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = esc($_POST['username']);
    $password = $_POST['password'];
    
    // Query user
    $result = $koneksi->query("SELECT * FROM users WHERE username='$username' AND status_aktif=1");
    
    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Verifikasi password
        if (password_verify($password, $user['password'])) {
            // Login berhasil
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'] ?? $user['username'] ?? 'Administrator';
            $_SESSION['role'] = $user['role'];
            $_SESSION['last_activity'] = time();
            
            // Update last login
            $koneksi->query("UPDATE users SET last_login=NOW() WHERE id={$user['id']}");
            
            header("Location: " . loginRedirectTargetByRole((string) $user['role']));
            exit;
        } else {
            $error = 'Password salah!';
        }
    } else {
        $error = 'Username tidak ditemukan atau akun tidak aktif!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - E-Recepsionis System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary: #2563eb;
            --secondary: #0369a1;
        }
        
        body {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .login-container {
            width: 100%;
            max-width: 450px;
            padding: 20px;
        }
        
        .login-card {
            background: white;
            border-radius: 24px;
            padding: 50px 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: slideUp 0.5s ease-out;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .login-logo {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .login-logo i {
            font-size: 4rem;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: inline-block;
            margin-bottom: 15px;
        }
        
        .login-logo h1 {
            font-size: 1.8rem;
            font-weight: 800;
            color: #1e293b;
            margin: 0;
        }
        
        .login-logo p {
            color: #64748b;
            margin: 5px 0 0 0;
            font-size: 0.95rem;
        }
        
        .form-label {
            font-weight: 600;
            color: #334155;
            margin-bottom: 8px;
        }
        
        .form-control {
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 1rem;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }
        
        .input-group-text {
            border: 2px solid #e2e8f0;
            border-right: none;
            border-radius: 12px 0 0 12px;
            background: #f8fafc;
            color: #64748b;
        }
        
        .input-group .form-control {
            border-left: none;
            border-radius: 0 12px 12px 0;
        }
        
        .input-group:focus-within .input-group-text {
            border-color: var(--primary);
        }
        
        .btn-login {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border: none;
            color: white;
            font-weight: 700;
            padding: 14px;
            border-radius: 12px;
            font-size: 1rem;
            width: 100%;
            transition: all 0.3s;
            margin-top: 10px;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
            color: white;
        }
        
        .alert {
            border: none;
            border-radius: 12px;
            padding: 12px 16px;
        }
        
        .alert-danger {
            background: #fee2e2;
            color: #991b1b;
        }
        
        .credential-info {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
            border-radius: 12px;
            padding: 15px;
            margin-top: 20px;
            border: 1px solid rgba(102, 126, 234, 0.2);
        }
        
        .credential-info h6 {
            color: var(--primary);
            font-weight: 700;
            margin-bottom: 10px;
            font-size: 0.9rem;
        }
        
        .credential-info p {
            margin: 5px 0;
            font-size: 0.85rem;
            color: #475569;
        }
        
        .credential-info code {
            background: white;
            padding: 2px 8px;
            border-radius: 6px;
            color: var(--secondary);
            font-weight: 600;
        }
        
        .position-relative {
            position: relative;
        }
        
        .password-toggle {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #64748b;
            z-index: 10;
        }

        @media (max-width: 575.98px) {
            body {
                align-items: flex-start;
                padding: 1rem 0;
            }

            .login-container {
                padding: 0.75rem;
                max-width: 100%;
            }

            .login-card {
                padding: 1.75rem 1.25rem;
                border-radius: 16px;
            }

            .login-logo i {
                font-size: 3rem;
            }

            .login-logo h1 {
                font-size: 1.45rem;
            }

            .form-control {
                font-size: 16px;
            }

            .btn-login {
                min-height: 48px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-logo">
                <i class="bi bi-shield-lock-fill"></i>
                <h1>Admin Login</h1>
                <p>E-Recepsionis System</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle-fill"></i> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success" style="background: #d1fae5; color: #065f46;">
                    <i class="bi bi-check-circle-fill"></i> <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="username" class="form-label">
                        <i class="bi bi-person-fill"></i> Username
                    </label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-person"></i>
                        </span>
                        <input type="text" 
                               class="form-control" 
                               id="username" 
                               name="username" 
                               placeholder="Masukkan username" 
                               required 
                               autofocus>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="password" class="form-label">
                        <i class="bi bi-lock-fill"></i> Password
                    </label>
                    <div class="position-relative">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-lock"></i>
                            </span>
                            <input type="password" 
                                   class="form-control" 
                                   id="password" 
                                   name="password" 
                                   placeholder="Masukkan password" 
                                   required>
                        </div>
                        <i class="bi bi-eye password-toggle" id="togglePassword"></i>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-login">
                    <i class="bi bi-box-arrow-in-right"></i> Login
                </button>
            </form>
            
            <div class="credential-info">
                <h6><i class="bi bi-info-circle-fill"></i> Default Login:</h6>
                <p><strong>Username:</strong> <code>admin</code></p>
                <p><strong>Password:</strong> <code>admin123</code></p>
            </div>
        </div>
    </div>
    
    <script>
        // Toggle password visibility
        const togglePassword = document.getElementById('togglePassword');
        const password = document.getElementById('password');
        
        togglePassword.addEventListener('click', function() {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            
            this.classList.toggle('bi-eye');
            this.classList.toggle('bi-eye-slash');
        });
        
        // Prevent double submit
        document.querySelector('form').addEventListener('submit', function() {
            const btn = this.querySelector('button[type="submit"]');
            btn.disabled = true;
            btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Loading...';
        });
    </script>
</body>
</html>
