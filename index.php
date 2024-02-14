<?php
session_start(); // 开启会话，这样就可以通过 $_SESSION 在多个页面间共享信息

// 读取或初始化IP记录文件
$ipFile = 'ipjl.json'; // 定义用于存储IP记录的文件名称
// 判断文件是否存在，如果存在则读取文件内容并解码成数组，如果不存在则初始化为空数组
$ipRecords = file_exists($ipFile) ? json_decode(file_get_contents($ipFile), true) : [];

// 检测用户IP是否允许提交
$userIP = $_SERVER['REMOTE_ADDR']; // 获取用户的IP地址
$canSubmit = true; // 默认设置为允许提交，后续根据条件修改此变量

// 判断用户IP是否已在记录中，并且根据不同状态决定是否允许提交
if (array_key_exists($userIP, $ipRecords)) {
    $lastSubmitTime = $ipRecords[$userIP]['lastSubmitTime']; // 获取上次提交时间
    $status = $ipRecords[$userIP]['status']; // 获取IP的审核状态
    // 根据状态和时间判断是否允许提交
    if ($status === '审核中' || $status === '已同意') {
        $canSubmit = false; // 如果状态是审核中或已同意，则不允许提交
    } elseif ($status === '已拒绝' && (time() - $lastSubmitTime) < 24 * 60 * 60) {
        $canSubmit = false; // 如果状态是已拒绝且未过24小时，则不允许提交
    }
}

// 处理用户提交的表单
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $canSubmit) {
    // 如果请求方法是POST且允许提交，则处理表单
    // 更新IP记录，包括最后提交时间和状态
    $ipRecords[$userIP] = [
        'lastSubmitTime' => time(), // 更新为当前时间
        'status' => '审核中', // 设置状态为审核中
    ];
    file_put_contents($ipFile, json_encode($ipRecords)); // 将更新后的记录保存到文件
    
    // 显示提交成功消息，并终止脚本执行
    echo "提交成功，您的申请正在审核中。";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>欢迎加入我们</title>
    <!-- 引入Bootstrap的CSS文件，用于快速美化页面 -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>欢迎加入我们</h1>
        <p>在您加入之前请填写您的游戏ID</p>
        <p class="text-danger">请填写如果输入的不是您正在游玩的游戏ID后果自负</p>
        <?php if (!$canSubmit): ?>
            <!-- 如果不允许提交，显示警告信息 -->
            <div class="alert alert-warning" role="alert">
                您暂时不能提交新的申请。请稍后再试。
            </div>
        <?php else: ?>
            <!-- 如果允许提交，显示表单让用户填写 -->
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

    <!-- 引入Bootstrap的JS库和其依赖项，用于页面的交互效果 -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
