<?php
session_start();

// 读取或初始化IP记录文件
$ipFile = 'ipjl.json';
$ipRecords = file_exists($ipFile) ? json_decode(file_get_contents($ipFile), true) : [];

// 检测用户IP是否允许提交
$userIP = $_SERVER['REMOTE_ADDR']; // 获取用户IP地址
$canSubmit = true; // 默认允许提交
if (array_key_exists($userIP, $ipRecords)) {
    $lastSubmitTime = $ipRecords[$userIP]['lastSubmitTime'];
    $status = $ipRecords[$userIP]['status'];
    if ($status === '审核中' || $status === '已同意') {
        $canSubmit = false; // 如果状态是审核中或已同意，则不允许提交
    } elseif ($status === '已拒绝' && (time() - $lastSubmitTime) < 24 * 60 * 60) {
        $canSubmit = false; // 如果状态是已拒绝且未过24小时，不允许提交
    }
}

// 处理用户提交的表单
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $canSubmit) {
    // 这里将调用 process.php 的逻辑处理提交的数据...
    // 注意：实际处理逻辑应该在 process.php 中完成，这里只是简化展示
    // 更新IP记录
    $ipRecords[$userIP] = [
        'lastSubmitTime' => time(),
        'status' => '审核中', // 默认设置为审核中
    ];
    file_put_contents($ipFile, json_encode($ipRecords));
    
    // 假设提交成功，重定向或显示成功消息
    echo "提交成功，您的申请正在审核中。";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>欢迎加入茶话会</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>欢迎加入我们</h1>
        <p>在您加入之前请填写您的游戏ID</p>
        <p class="text-danger">请填写如果输入的不是您正在游玩的游戏ID后果自负</p>
        <?php if (!$canSubmit): ?>
            <div class="alert alert-warning" role="alert">
                您暂时不能提交新的申请。请稍后再试。
            </div>
        <?php else: ?>
            <form action="process.php" method="POST">
                <div class="form-group">
                    <label for="gameID">游戏ID</label>
                    <input type="text" class="form-control" id="gameID" name="gameID" required>
                </div>
                <div class="form-group">
                    <label for="qqNumber">QQ号（确保您的是一直使用的QQ号）</label>
                    <input type="text" class="form-control" id="qqNumber" name="qqNumber" required>
                </div>
                <button type="submit" class="btn btn-primary">提交申请</button>
            </form>
        <?php endif; ?>
    </div>

    <!-- 引入Bootstrap JS 和依赖项 -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
