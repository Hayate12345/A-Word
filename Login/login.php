<?php

// error回避のために配列を初期化
$error = [];
$email = '';
$password = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

    if ($password === '') {
        $error['login2'] = 'blank';
    } else {
        // データベースと接続
        $db = new mysqli('localhost', 'root', 'root', 'kadai_db');

        // SQL発行
        $stmt = $db->prepare('select id, name, password from members where email=? limit 1');
        $stmt->bind_param('s', $email);

        // SQL事項
        $success = $stmt->execute();
        $stmt->bind_result($id, $name, $hash);
        $stmt->fetch();

        // ログインチェック
        if (password_verify($password, $hash)) {
            // ログイン成功
            // セッションID生成
            session_start();
            $_SESSION['id'] = session_id();
            $_SESSION['user_id'] = $id;
            $_SESSION['user_name'] = $name;
            header('Location: ../community_home/home.php');
            exit();
        } else {
            // ログイン失敗時はエラーメッセージを表示
            $error['login'] = 'failed';
        }
    }
}
?>

<!DOCTYPE html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/login.css">
    <title>Log in</title>
</head>

<body>
    <div class="card">
        <div class="content">
            <h2>Log in</h2>
            <form action="" method="post">

                <div class="user-box">
                    <label>Email</label>
                    <input type="text" name="email" size="35" maxlength="255" value="<?php echo htmlspecialchars($email); ?>" required>
                </div>

                <div class="user-box">
                    <label>Password</label>
                    <input type="password" name="password" size="35" maxlength="255" value="<?php echo htmlspecialchars($password); ?>" required>

                    <!-- ログイン失敗のエラーメッセージ -->
                    <?php if (isset($error['login']) && $error['login'] === 'failed') : ?>
                        <p class="error">*ログインに失敗しました。正しくご記入ください。</p>
                    <?php endif; ?>
                </div>

                <button>
                    <span></span>
                    <span></span>
                    <span></span>
                    <span></span>
                    Log in
                </button>
            </form>

            <div class="guidance">
                <a href="../Register/welcome.php">会員登録はこちら&gt;&gt;</a>
            </div>
        </div>
    </div>
</body>

</html>