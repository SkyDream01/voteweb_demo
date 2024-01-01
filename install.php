<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 获取用户输入的数据库配置信息
    $hostname = $_POST['hostname'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $database = $_POST['database'];

    // 创建数据库连接
    $conn = new mysqli($hostname, $username, $password);

    // 检查连接是否成功
    if ($conn->connect_error) {
        die("连接失败: " . $conn->connect_error);
    }

    // 创建数据库
    $sql = "CREATE DATABASE IF NOT EXISTS $database";
    if ($conn->query($sql) === TRUE) {
        echo "数据库创建成功";
    } else {
        echo "Error creating database: " . $conn->error;
    }

    // 选择数据库
    $conn->select_db($database);

    // 创建投票表
    $sql = "CREATE TABLE IF NOT EXISTS votes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        item_name VARCHAR(255) NOT NULL,
        option_name VARCHAR(255) NOT NULL,
        vote_count INT DEFAULT 0
    )";
    if ($conn->query($sql) === TRUE) {
        echo "表格创建成功";
    } else {
        echo "Error creating table: " . $conn->error;
    }

    // 关闭连接
    $conn->close();

    // 将配置信息保存到 config.php 文件
    $config_content = "<?php\n";
    $config_content .= "\$hostname = '$hostname';\n";
    $config_content .= "\$username = '$username';\n";
    $config_content .= "\$password = " . (empty($password) ? "''" : "'$password'") . ";\n";
    $config_content .= "\$database = '$database';\n";
    $config_content .= "?>";

    file_put_contents('config.php', $config_content);

    echo "<br>配置文件已创建成功";
} else {
    // 显示配置表单
    echo "<form method='post'>
        <label>主机名：</label>
        <input type='text' name='hostname' required><br>
        <label>用户名：</label>
        <input type='text' name='username' required><br>
        <label>密码：</label>
        <input type='password' name='password'><br>
        <label>数据库名：</label>
        <input type='text' name='database' required><br>
        <input type='submit' value='安装'>
    </form>";
}
?>
