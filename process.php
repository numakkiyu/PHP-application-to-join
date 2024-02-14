<?php
session_start(); // 开启会话，用于在不同页面之间保持状态

// 定义申请数据文件的路径
$dataFile = 'sqid.json';
// 如果文件存在，则读取并解码为数组；如果不存在，初始化为空数组
$applications = file_exists($dataFile) ? json_decode(file_get_contents($dataFile), true) : [];

// 处理用户提交的申请
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['gameID']) && isset($_POST['qqNumber'])) {
    // 检查是否通过POST方法提交，并且游戏ID与QQ号都已设置

    // 模拟查询游戏等级（未连接游戏API），这里用随机数模拟真实游戏等级
    $gameLevel = rand(1, 100);

    // 生成随机申请ID，用于唯一标识每个申请
    $applicationId = rand(100000, 999999);

    // 将新的申请信息添加到申请数组中
    $applications[] = [
        'gameID' => $_POST['gameID'], // 提交的游戏ID
        'qqNumber' => $_POST['qqNumber'], // 提交的QQ号
        'gameLevel' => $gameLevel, // 模拟的游戏等级
        'applicationId' => $applicationId, // 生成的申请ID
        'status' => '审核中' // 新申请的默认状态
    ];

    // 将更新后的申请数组编码为JSON字符串并保存到文件
    file_put_contents($dataFile, json_encode($applications));

    // 向用户显示申请已提交的消息，并提供申请ID
    echo "申请已提交。您的申请ID是 {$applicationId}。请保存此ID以查询申请状态。";
    exit; // 结束脚本执行
}

// 处理管理员审批操作
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['isLoggedIn']) && $_SESSION['isLoggedIn'] === true && isset($_POST['applicationId']) && isset($_POST['action'])) {
    // 检查是否通过POST方法提交，管理员是否已登录，以及申请ID和操作是否已设置

    $applicationId = $_POST['applicationId']; // 获取提交的申请ID
    $action = $_POST['action']; // 获取审批操作（接受或拒绝）

    foreach ($applications as &$application) {
        // 遍历所有申请，寻找与提交的申请ID匹配的申请
        if ($application['applicationId'] == $applicationId) {
            // 根据操作更新申请状态
            if ($action === 'accept') {
                $application['status'] = '已同意';
            } elseif ($action === 'reject') {
                $application['status'] = '已拒绝';
            }
            // 保存更新后的申请数据到文件
            file_put_contents($dataFile, json_encode($applications));
            break; // 更新完成后退出循环
        }
    }
    unset($application); // 解除引用

    // 重定向回管理员页面，以便管理员可以看到更新后的申请状态
    header('Location: admin.php');
    exit; // 结束脚本执行
}

// 如果脚本不是通过POST方法调用或不符合上述任何条件，则重定向到首页
header('Location: index.php');
exit; // 结束脚本执行
