<?php
session_start(); // 开启会话，这样可以在整个会话中使用 $_SESSION 来存取数据

// 检查管理员是否已登录
if (!isset($_SESSION['isLoggedIn']) || $_SESSION['isLoggedIn'] !== true) {
    // 如果管理员未登录，重定向到登录页面
    header('Location: login.php');
    exit; // 终止脚本继续执行
}

// 读取申请数据
$dataFile = 'sqid.json'; // 定义存储申请数据的文件名
$applications = json_decode(file_get_contents($dataFile), true); // 读取文件内容并解码为数组

// 处理审批操作
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['applicationId']) && isset($_POST['action'])) {
    // 检查是否是POST请求并且请求中包含必要的参数
    $applicationId = $_POST['applicationId']; // 获取提交的申请ID
    $action = $_POST['action']; // 获取操作类型（接受或拒绝）

    foreach ($applications as &$application) {
        // 遍历申请数据，寻找匹配的申请ID
        if ($application['applicationId'] == $applicationId) {
            // 找到匹配的申请，根据操作类型更新状态
            if ($action === 'accept') {
                $application['status'] = '已同意';
            } elseif ($action === 'reject') {
                $application['status'] = '已拒绝';
            }
            // 保存更改到文件
            file_put_contents($dataFile, json_encode($applications));
            break; // 找到后即退出循环
        }
    }
    unset($application); // 解除引用，避免后续不小心修改
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理员界面</title>
    <!-- 引入Bootstrap CSS库，用于页面美化 -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>管理员界面</h2>
        <div class="mb-3">
            <!-- 退出登录的链接 -->
            <a href="logout.php" class="btn btn-danger">退出管理员登录</a>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">游戏ID</th>
                    <th scope="col">QQ号</th>
                    <th scope="col">游戏等级</th>
                    <th scope="col">申请ID</th>
                    <th scope="col">状态</th>
                    <th scope="col">操作</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($applications as $index => $application): ?>
                <tr>
                    <th scope="row"><?php echo $index + 1; ?></th> <!-- 序号 -->
                    <td><?php echo htmlspecialchars($application['gameID']); ?></td> <!-- 游戏ID -->
                    <td><?php echo htmlspecialchars($application['qqNumber']); ?></td> <!-- QQ号 -->
                    <td><?php echo htmlspecialchars($application['gameLevel']); ?></td> <!-- 游戏等级 -->
                    <td><?php echo htmlspecialchars($application['applicationId']); ?></td> <!-- 申请ID -->
                    <td><?php echo htmlspecialchars($application['status']); ?></td> <!-- 申请状态 -->
                    <td>
                        <?php if ($application['status'] === '审核中'): ?>
                        <!-- 如果申请状态是审核中，显示操作按钮 -->
                        <form action="admin.php" method="post">
                            <input type="hidden" name="applicationId" value="<?php echo $application['applicationId']; ?>">
                            <!-- 同意按钮 -->
                            <button type="submit" name="action" value="accept" class="btn btn-success btn-sm">同意</button>
                            <!-- 拒绝按钮 -->
                            <button type="submit" name="action" value="reject" class="btn btn-danger btn-sm">拒绝</button>
                        </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- 引入Bootstrap的JS库及其依赖，用于增强页面的交互性 -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
