<?php
/**
 * Login Page
 */

// Zaten giriş yapılmışsa dashboard'a yönlendir
if (isLoggedIn()) {
    redirect('index.php?page=dashboard');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $loginType = post('login_type', 'user');
    $username = post('username');
    $password = post('password');
    
    if (empty($username) || empty($password)) {
        $error = 'Lütfen tüm alanları doldurun.';
    } else {
        if ($loginType === 'student') {
            // Öğrenci girişi
            $studentModel = new Student();
            $student = $studentModel->login($username, $password);
            
            if ($student) {
                $_SESSION['student_id'] = $student['id'];
                $_SESSION['role'] = 'student';
                $_SESSION['full_name'] = $student['full_name'];
                $_SESSION['student_number'] = $student['student_number'];
                
                setFlash('success', 'Hoş geldiniz, ' . clean($student['full_name']));
                redirect('index.php?page=dashboard');
            } else {
                $error = 'Öğrenci numarası veya şifre hatalı.';
            }
        } else {
            // Kullanıcı (Admin/Öğretmen) girişi
            $userModel = new User();
            $user = $userModel->login($username, $password);
            
            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['username'] = $user['username'];
                
                setFlash('success', 'Hoş geldiniz, ' . clean($user['full_name']));
                redirect('index.php?page=dashboard');
            } else {
                $error = 'Kullanıcı adı veya şifre hatalı.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .login-container {
            max-width: 450px;
            margin: 0 auto;
        }
        .login-card {
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 15px 15px 0 0;
            text-align: center;
        }
        .login-body {
            padding: 40px;
        }
        .btn-login {
            padding: 12px;
            font-size: 16px;
            font-weight: 600;
        }
        .nav-pills .nav-link {
            border-radius: 10px;
        }
        .nav-pills .nav-link.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="card login-card">
                <div class="login-header">
                    <i class="bi bi-shield-lock fs-1 mb-3"></i>
                    <h3 class="mb-0"><?php echo SITE_NAME; ?></h3>
                    <p class="mb-0 mt-2">Sisteme Giriş</p>
                </div>
                <div class="login-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i><?php echo clean($error); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Login Type Tabs -->
                    <ul class="nav nav-pills nav-fill mb-4" id="loginTypeTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="user-tab" data-bs-toggle="pill" data-bs-target="#user-login" type="button">
                                <i class="bi bi-person-badge me-2"></i>Kullanıcı
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="student-tab" data-bs-toggle="pill" data-bs-target="#student-login" type="button">
                                <i class="bi bi-person me-2"></i>Öğrenci
                            </button>
                        </li>
                    </ul>
                    
                    <div class="tab-content" id="loginTypeContent">
                        <!-- User Login -->
                        <div class="tab-pane fade show active" id="user-login" role="tabpanel">
                            <form method="POST" action="">
                                <input type="hidden" name="login_type" value="user">
                                
                                <div class="mb-3">
                                    <label for="username" class="form-label">Kullanıcı Adı</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                                        <input type="text" class="form-control" id="username" name="username" required autofocus>
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <label for="password" class="form-label">Şifre</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                        <input type="password" class="form-control" id="password" name="password" required>
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn btn-primary w-100 btn-login">
                                    <i class="bi bi-box-arrow-in-right me-2"></i>Giriş Yap
                                </button>
                            </form>
                        </div>
                        
                        <!-- Student Login -->
                        <div class="tab-pane fade" id="student-login" role="tabpanel">
                            <form method="POST" action="">
                                <input type="hidden" name="login_type" value="student">
                                
                                <div class="mb-3">
                                    <label for="student_number" class="form-label">Öğrenci Numarası</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                                        <input type="text" class="form-control" id="student_number" name="username" required>
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <label for="student_password" class="form-label">Şifre</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                        <input type="password" class="form-control" id="student_password" name="password" required>
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn btn-primary w-100 btn-login">
                                    <i class="bi bi-box-arrow-in-right me-2"></i>Giriş Yap
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <div class="text-center text-muted small">
                        <p class="mb-1"><strong>Demo Hesaplar:</strong></p>
                        <p class="mb-1">Admin: <code>admin</code> / <code>admin123</code></p>
                        <p class="mb-1">Öğretmen: <code>ogretmen</code> / <code>ogretmen123</code></p>
                        <p class="mb-0">Öğrenci: <code>2024001</code> / <code>ogrenci123</code></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
