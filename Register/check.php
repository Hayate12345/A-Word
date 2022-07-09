<?php

// セッションスタート
session_start();

// データを受け取る
if (isset($_SESSION['form'])) {
    $form = $_SESSION['form'];
} else {

    // データがない場合はユーザー登録ページへ飛ばす
    header('Location: welcome.php');
    exit();
}

// データベースとの連携
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // データベースとの接続
    $db = new mysqli('localhost', 'root', 'root', 'kadai_db');

    // データを登録する
    $stmt = $db->prepare('insert into members (name, email, password, picture) VALUES (?, ?, ?, ?)');

    // パスワードの暗号化
    $password = password_hash($form['password'], PASSWORD_DEFAULT);
    $stmt->bind_param('ssss', $form['nickname'], $form['email'], $password, $form['image']);

    // SQL実行
    $success = $stmt->execute();

    // 重複登録を防ぐためにデータを消去しておく
    unset($_SESSION['form']);

    // ログインページに飛ばす
    header('Location: ../Login/login.php');
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/check.css">
    <title>登録情報確認</title>
</head>

<body>
    <div class="card">
        <div class="content">
            <h2>Check</h2>

            <form action="" method="post">
                <div class="user-box">
                    <label>Nick Name</label>
                    <br>
                    <p>
                        <?php echo htmlspecialchars($form['nickname']); ?>
                    </p>
                </div>

                <div class="user-box">
                    <label>Email</label>
                    <br>
                    <p>
                        <?php echo htmlspecialchars($form['email']); ?>
                    </p>
                </div>

                <div class="user-box">
                    <label>Password</label>
                    <br>
                    <p>
                        <?php echo htmlspecialchars($form['password']); ?>
                    </p>
                </div>

                <div class="user-box">
                    <label>Icon</label>
                    <br>
                    <p>
                        <img src="../member_picture/<?php echo htmlspecialchars($form['image']); ?>" height="80" width="80" alt="アイコン">
                    </p>
                </div>

                <button>
                    <span></span>
                    <span></span>
                    <span></span>
                    <span></span>
                    Sign up
                </button>
            </form>

            <!--書き直しができるように値(Tofix)を設定しておく-->
            <div class="guidance">
                <a href="welcome.php?action=Tofix">書き直す</a>
            </div>
        </div>
    </div>
</body>

</html>