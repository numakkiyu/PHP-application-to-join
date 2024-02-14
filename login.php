<?php
// 开启会话，用于跟踪用户登录状态
session_start();

// 假设的管理员账号和密码，实际应用中应从数据库或安全的存储中获取
// 注意这个php没有任何安全组件，所以请及时更改
$adminUsername = 'admin'; // 管理员用户名
$adminPassword = 'password'; // 管理员密码

// 检查是否已经登录
if (isset($_SESSION['isLoggedIn']) && $_SESSION['isLoggedIn'] === true) {
    // 如果已登录，重定向到管理员页面
    header('Location: admin.php');
    exit; // 终止脚本执行
}

$loginError = ''; // 登录错误信息初始化为空

// 检查表单是否提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 提取表单提交的用户名和密码
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // 验证用户名和密码
    if ($username === $adminUsername && $password === $adminPassword) {
        // 登录成功，将登录状态存入会话
        $_SESSION['isLoggedIn'] = true;
        // 重定向到管理员页面
        header('Location: admin.php');
        exit; // 终止脚本执行
    } else {
        // 登录失败，设置错误信息
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
            <!-- 显示登录错误信息 -->
            <div class="alert alert-danger" role="alert">
                <?php echo $loginError; ?>
            </div>
        <?php endif; ?>

        <!-- 登录表单 -->
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
