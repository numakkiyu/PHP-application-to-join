<?php
session_start();

// 检查管理员是否已登录
if (!isset($_SESSION['isLoggedIn']) || $_SESSION['isLoggedIn'] !== true) {
    header('Location: login.php');
    exit;
}

// 读取申请数据
$dataFile = 'sqid.json';
$applications = json_decode(file_get_contents($dataFile), true);

// 处理审批操作
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['applicationId']) && isset($_POST['action'])) {
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
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理员界面</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>管理员界面</h2>
        <div class="mb-3">
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
                    <th scope="row"><?php echo $index + 1; ?></th>
                    <td><?php echo htmlspecialchars($application['gameID']); ?></td>
                    <td><?php echo htmlspecialchars($application['qqNumber']); ?></td>
                    <td><?php echo htmlspecialchars($application['gameLevel']); ?></td>
                    <td><?php echo htmlspecialchars($application['applicationId']); ?></td>
                    <td><?php echo htmlspecialchars($application['status']); ?></td>
                    <td>
                        <?php if ($application['status'] === '审核中'): ?>
                        <form action="admin.php" method="post">
                            <input type="hidden" name="applicationId" value="<?php echo $application['applicationId']; ?>">
                            <button type="submit" name="action" value="accept" class="btn btn-success btn-sm">同意</button>
                            <button type="submit" name="action" value="reject" class="btn btn-danger btn-sm">拒绝</button>
                        </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
