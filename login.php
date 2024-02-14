<?php
// 简单的登录认证逻辑
session_start();

// 假设的管理员账号和密码，实际应用中应从数据库或安全的存储中获取
$adminUsername = 'admin';
$adminPassword = 'password';

// 检查是否已经登录
if (isset($_SESSION['isLoggedIn']) && $_SESSION['isLoggedIn'] === true) {
    header('Location: admin.php');
    exit;
}

$loginError = '';

// 检查表单是否提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // 验证用户名和密码
    if ($username === $adminUsername && $password === $adminPassword) {
        // 登录成功
        $_SESSION['isLoggedIn'] = true;
        header('Location: admin.php');
        exit;
    } else {
        $loginError = '用户名或密码错误';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理员登录</title>
    <!-- 引入Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">管理员登录</h2>
        
        <?php if ($loginError): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $loginError; ?>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="username">用户名</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">密码</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">登录</button>
        </form>
    </div>

    <!-- 引入Bootstrap JS 和依赖项 -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
