<?php
// 引用数据库配置文件
require_once 'config.php';

// 创建数据库连接
$conn = new mysqli($hostname, $username, $password, $database);

// 处理添加选项请求
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $item_name = $_POST['item_name'];

    // 获取输入的选项，使用逗号分隔
    $options_input = $_POST['options'];
    $options = explode(',', $options_input);

    // 插入选项
    foreach ($options as $option) {
        $option_name = trim($option);
        if (!empty($option_name)) {
            $sql = "INSERT INTO votes (item_name, option_name) VALUES ('$item_name', '$option_name')";
            $conn->query($sql);
        }
    }
}

// 处理删除投票项和选项请求
if (isset($_GET['delete_item'])) {
    $item_name = $_GET['delete_item'];

    // 删除投票项及其关联的选项
    $sql_delete_item = "DELETE FROM votes WHERE item_name='$item_name'";
    $conn->query($sql_delete_item);

    header("Location: admin.php");
}

// 获取所有投票项
$sql_items = "SELECT DISTINCT item_name FROM votes";
$result_items = $conn->query($sql_items);

if ($result_items->num_rows > 0) {
    while ($row_item = $result_items->fetch_assoc()) {
        $item_name = $row_item['item_name'];
        echo "<h3>$item_name</h3>";

        // 获取选项和投票计数
        $sql_options = "SELECT option_name, vote_count FROM votes WHERE item_name='$item_name'";
        $result_options = $conn->query($sql_options);

        if ($result_options->num_rows > 0) {
            while ($row_option = $result_options->fetch_assoc()) {
                $option_name = $row_option['option_name'];
                $vote_count = $row_option['vote_count'];
                echo "<p>$option_name: $vote_count 票</p>";
            }
        } else {
            echo "暂无选项";
        }

        // 添加删除投票项按钮
        echo "<p><a href='admin.php?delete_item=$item_name'>删除投票项</a></p>";
    }
} else {
    echo "暂无投票项";
}

// 添加选项表单
echo "<form method='post'>
    <label>投票项：</label>
    <input type='text' name='item_name' required><br>
    <label>选项（用逗号分隔）：</label>
    <input type='text' name='options' required><br>
    <input type='submit' value='添加选项'></form>";

// 关闭数据库连接
$conn->close();
?>
