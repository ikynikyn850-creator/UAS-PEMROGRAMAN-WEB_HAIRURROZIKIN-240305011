<?php
session_start();
if (isset($_SESSION['user'])) {
    header('Location: UTS_PEMROGAMAN WEB_M. HAIRURROZIKIN 240305011/index.html');
    exit;
}
$error = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : ''; // Menangkap pesan error dari URL jika ada, dan menyimpannya dalam variabel $error. Fungsi htmlspecialchars digunakan untuk mencegah serangan XSS dengan mengubah karakter khusus menjadi entitas HTML.
?>

<!doctype html> 
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - LAPORAN KEGIATAN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        body.login-body {
            background: linear-gradient(135deg, #7C3AED 0%, #6D28D9 25%, #4C1D95 50%, #6D28D9 75%, #7C3AED 100%);
            min-height: 100vh;
            overflow: hidden;
        }

        .login-container {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            z-index: 10;
        }

        .login-card {
            position: relative;
            z-index: 10;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(168, 85, 247, 0.2);
            box-shadow: 0 20px 60px rgba(109, 40, 217, 0.3);
            max-width: 450px;
            width: 100%;
        }

        .login-card .card-body {
            padding: 3.5rem;
        }

        .login-title {
            font-size: 2rem;
            font-weight: 800;
            background: linear-gradient(135deg, #7C3AED, #6D28D9);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: 1.5px;
            margin-bottom: 0.5rem;
        }

        .login-subtitle {
            color: #6B7280;
            font-size: 0.95rem;
            line-height: 1.6;
        }

        .form-group-login {
            margin-bottom: 1.5rem;
        }

        .form-group-login label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.7rem;
            display: block;
            font-size: 0.95rem;
        }

        .form-control-login {
            border-radius: 12px;
            border: 2px solid rgba(139, 92, 246, 0.2);
            padding: 0.75rem 1rem;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: rgba(237, 233, 254, 0.3);
        }

        .form-control-login:focus {
            box-shadow: 0 0 0 0.2rem rgba(139, 92, 246, 0.25);
            border-color: #7C3AED;
            background: rgba(237, 233, 254, 0.6);
        }

        .form-control-login::placeholder {
            color: rgba(107, 114, 128, 0.6);
        }

        .btn-login {
            background: linear-gradient(135deg, #8B5CF6, #6D28D9);
            border: none;
            color: white;
            font-weight: 700;
            font-size: 1rem;
            padding: 0.85rem 1.5rem;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(139, 92, 246, 0.4);
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            width: 100%;
        }

        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(109, 40, 217, 0.6);
            background: linear-gradient(135deg, #A78BFA, #7C3AED);
            color: white;
        }

        .btn-login:active {
            transform: translateY(-1px);
        }

        .alert-login {
            border-radius: 12px;
            border: 1px solid rgba(239, 68, 68, 0.3);
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.1), rgba(239, 68, 68, 0.05));
            color: #7f1d1d;
            animation: slideInDown 0.5s ease-out;
        }

        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body class="login-body">
    <div class="login-container">
        <div class="card login-card border-0 rounded-4">
            <div class="card-body">
                <h1 class="login-title text-center">E-PORTO KEGIATAN</h1>
                <p class="login-subtitle text-center mb-4"> Portofolio dan laporan kegiatan  </p>

                <?php if ($error): ?> 
                    <div class="alert alert-danger alert-login mb-4"> 
                        <i class="bi bi-exclamation-circle me-2"></i><?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form action="login.php" method="post">
                    <div class="form-group-login">
                        <label for="username"><i class="bi bi-person-circle me-2"></i>Username</label>
                        <input type="text" id="username" name="username" class="form-control form-control-login" placeholder="Masukkan username Anda" required autofocus>
                    </div>

                    <div class="form-group-login mb-4">
                        <label for="password"><i class="bi bi-lock-circle me-2"></i>Password</label>
                        <input type="password" id="password" name="password" class="form-control form-control-login" placeholder="Masukkan password Anda" required>
                    </div>

                    <button type="submit" class="btn btn-login">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Masuk Sekarang
                    </button>
                </form>

                <p class="text-center text-muted mt-4 mb-0" style="font-size: 0.85rem;">
                    <i class="bi bi-shield-check me-1"></i>Login  aman dan terlindungi
                </p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
</body>
</html>
