<?php
// セッションスタート
session_start();

// 書き直しのために指定した値(Tofix)を呼び出す
if (isset($_GET['action']) && $_GET['action'] === 'Tofix' && isset($_SESSION['form'])) {
    $form = $_SESSION['form'];
} else {
    // エラー回避のために配列を初期化しておく
    $form = [
        'nickname' => '',
        'email' => '',
        'password' => '',
    ];
}

// エラー回避のための配列初期化
$error = [];

// フォームの入力内容をチェックする
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // nameの入力内容をチェックする
    $form['nickname'] = filter_input(INPUT_POST, 'nickname', FILTER_SANITIZE_STRING);


    // emailの入力内容をチェックする
    $form['email'] = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    if ($form['email'] === '') {
        $error['email'] = 'blank';
    } else {
        // メールアドレスの重複を調べる
        // データベースとの接続
        $db =  new mysqli('localhost', 'root', 'root', 'kadai_db');

        // SQL発行
        $stmt = $db->prepare('select count(*) from members where email=?');
        $stmt->bind_param('s', $form['email']);

        // SQL実行
        $success = $stmt->execute();

        // countの結果を変数(Result_is)に代入
        $stmt->bind_result($Result_is);

        // 0か1で重複を判断する
        $stmt->fetch();

        // 数字が0以上ならば重複だから　$Result_isが0以上の時エラーを表示するという条件を追加する
        if ($Result_is > 0) {
            $error['email'] = 'duplicate';
        }
    }

    // passwordの入力内容をチェックする
    $form['password'] = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

    // パスワードの文字数を判定する
    if (strlen($form['password']) < 6) {
        $error['password'] = 'length';
    }

    // 画像をチェックする
    $image = $_FILES['image'];
    if ($image['name'] !== '' && $image['error'] === 0) {
        $type = mime_content_type($image['tmp_name']);

        // 写真の形式の制限
        if ($type !== 'image/jpeg' && $type !== 'image/png') {
            $error['image'] = 'type';
        }
    }

    if (empty($error)) {
        $_SESSION['form'] = $form;

        // 画像のアップロード
        if ($image['name'] !== '') {
            $filename = date('YmdHis') . '_' . $image['name'];
            if (!move_uploaded_file($image['tmp_name'], '../member_picture/' . $filename)) {
                die('ファイルのアップロードに失敗しました');
            }
            $_SESSION['form']['image'] = $filename;
        } else {
            $_SESSION['form']['image'] = '';
        }

        // すべてにエラーがない場合確認画面に移動する
        header('Location: check.php');
        exit();
    }
}
?>

<!-------------------------------------------------------------------------------------------------------------------------->

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/welcome.css">
    <title>ユーザー登録</title>
</head>

<body>
    <div class="card">
        <div class="content">
            <h2>sign up</h2>
            <form action="" method="post" enctype="multipart/form-data">

                <div class="user-box">
                    <label>Nick Name<span class="error">*</span></label>
                    <input type="text" name="nickname" required value="<?php echo htmlspecialchars($form['nickname']); ?>">
                </div>

                <div class="user-box">
                    <label>Email<span class="error">*</span></label>
                    <input type="email" name="email" required value="<?php echo htmlspecialchars($form['email']); ?>">
                    <!-- メールアドレスが重複している場合エラーを表示する -->
                    <?php if (isset($error['email']) && $error['email'] === 'duplicate') : ?>
                        <h5 class="error">*指定のメールアドレスは既に登録されています。ログインしてください!</h5>
                    <?php endif; ?>
                </div>

                <div class="user-box">
                    <label>Password<span class="error">*</span></label>
                    <input type="password" name="password" required value="">
                    <!-- パスワードが6文字以下の場合エラーを表示する -->
                    <?php if (isset($error['password']) && $error['password'] === 'length') : ?>
                        <h5 class="error">*パスワードは6文字以上で入力してください!</h5>
                    <?php endif; ?>
                </div>

                <div class="user-box">
                    <label>Icon<span class="error">*</span></label>
                    <input type="file" name="image" size="30" required value="">
                    <!-- 指定された画像が(JPG)形式でなければエラーを表示する -->
                    <?php if (isset($error['image']) && $error['image'] === 'type') : ?>
                        <h5 class="error">*画像はJPG形式またはPNG形式で指定してください!</h5>
                    <?php endif; ?>
                </div>

                <button>
                    <span></span>
                    <span></span>
                    <span></span>
                    <span></span>
                    Check
                </button>
            </form>
        </div>
    </div>
</body>

</html>