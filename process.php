<?php
session_start();

// 确保这个脚本既可以处理用户申请也可以处理管理员操作
$dataFile = 'sqid.json';
$applications = file_exists($dataFile) ? json_decode(file_get_contents($dataFile), true) : [];

// 处理用户提交的申请
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['gameID']) && isset($_POST['qqNumber'])) {
    // 模拟查询游戏等级（未连接游戏API）
    $gameLevel = rand(1, 100); // 示例使用随机数值代替游戏等级

    // 生成随机申请ID
    $applicationId = rand(100000, 999999);

    // 添加新的申请到数组
    $applications[] = [
        'gameID' => $_POST['gameID'],
        'qqNumber' => $_POST['qqNumber'],
        'gameLevel' => $gameLevel,
        'applicationId' => $applicationId,
        'status' => '审核中'
    ];

    // 保存数据到文件
    file_put_contents($dataFile, json_encode($applications));

    // 提示用户申请已提交
    echo "申请已提交。您的申请ID是 {$applicationId}。请保存此ID以查询申请状态。";
    exit;
}

// 处理管理员审批操作
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['isLoggedIn']) && $_SESSION['isLoggedIn'] === true && isset($_POST['applicationId']) && isset($_POST['action'])) {
    $applicationId = $_POST['applicationId'];
    $action = $_POST['action'];

    foreach ($applications as &$application) {
        if ($application['applicationId'] == $applicationId) {
            if ($action === 'accept') {
                $application['status'] = '已同意';
            } elseif ($action === 'reject') {
                $application['status'] = '已拒绝';
            }
            // 保存更改
            file_put_contents($dataFile, json_encode($applications));
            break;
        }
    }
    unset($application);

    // 重定向回管理员页面以显示更新后的状态
    header('Location: admin.php');
    exit;
}

// 如果脚本不是通过POST方法调用，重定向到首页
header('Location: index.php');
exit;
