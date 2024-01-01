<?php
// 引用数据库配置文件
require_once 'config.php';

// 创建数据库连接
$conn = new mysqli($hostname, $username, $password, $database);

// 检查连接是否成功
if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
}

// 处理投票请求
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $item_name = $_POST['item_name'];
    $option_name = $_POST['option_name'];

    // 更新投票数量
    $sql = "UPDATE votes SET vote_count = vote_count + 1 WHERE item_name='$item_name' AND option_name='$option_name'";
    $conn->query($sql);
}

// 获取投票选项
$sql = "SELECT DISTINCT item_name FROM votes";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // 显示投票选项
    while ($row = $result->fetch_assoc()) {
        $item_name = $row['item_name'];
        echo "<h3>$item_name</h3>";

        // 获取选项
        $sql_options = "SELECT option_name FROM votes WHERE item_name='$item_name'";
        $result_options = $conn->query($sql_options);

        // 显示选项
        while ($row_option = $result_options->fetch_assoc()) {
            $option_name = $row_option['option_name'];
            echo "<form method='post'><label>$option_name</label>
                <input type='hidden' name='item_name' value='$item_name'>
                <input type='hidden' name='option_name' value='$option_name'>
                <input type='submit' value='投票'></form>";
        }
    }
} else {
    echo "暂无投票项";
}

// 关闭数据库连接
$conn->close();
?>
