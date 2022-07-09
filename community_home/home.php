<?php
// sessionスタート
session_start();

// ログインしている場合
if (isset($_SESSION['id'])) {
    $id = $_SESSION['user_id'];
    $name = $_SESSION['user_name'];
} else {
    // ログインしていない場合、ログインページへ戻す
    header('Location: ../Login/login.php');
    exit();
}

// データベースとの接続
$db = new mysqli('localhost', 'root', 'root', 'kadai_db');

// メッセージの投稿
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);

    // SQL発行
    $stmt = $db->prepare('INSERT INTO posts (message, member_id) VALUES (?, ?)');
    $stmt->bind_param('si', $message, $id);

    // SQL実行
    $stmt->execute();

    // データベースの重複登録を防ぐ　POSTの内容を消す
    header('Location: ../community_home/home.php');
}

// 情報の呼び出し
$stmt = $db->prepare('select p.id, p.member_id, p.message, p.created, m.name, m.picture from posts p, members m where m.id=p.member_id order by id desc');

//　結果を変数におく
$stmt->bind_result($id, $member_id, $message, $created, $name, $picture);

// SQL実行
$stmt->execute();
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>A word</title>
    <link rel="stylesheet" href="../css/home.css">
    <script src="https://kit.fontawesome.com/67024cb754.js" crossorigin="anonymous"></script>
</head>

<body>
    <div class="container">
        <div class="left-menu">
            <div class="menu">
                <h3><i class="fa-solid fa-house"></i><a href="./home.php">ホーム</a></h3>
                <h3><i class="fa-solid fa-arrow-right-from-bracket"></i><a href="../Login/logout2.php">ログアウト</a></h3>
                <label class="open" for="pop-up"><i class="fa-solid fa-pen"></i>投稿する</label>
            </div>
            <input type="checkbox" id="pop-up">
            <div class="overlay">
                <div class="window">
                    <label class="close" for="pop-up">✖︎</label>
                    <form action="" method="post">
                        <dl>
                            <dd>
                                <textarea name="message" placeholder="いまどうしてる?" cols="60" rows="18" required></textarea>
                            </dd>
                        </dl>
                        <button><i class="fa-solid fa-pen"></i>投稿する</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="main-contents">
            <h1>ホーム</h1>

            <!-- 投稿結果をループ -->
            <?php
            while ($stmt->fetch()) :
            ?>
                <div class="posts">
                    <div class="post">

                        <!-- 写真の表示 -->
                        <img src="../member_picture/<?php echo htmlspecialchars($picture); ?>" alt="" width="70" height="70">

                        <li>
                            <p class="username">
                                <!-- ユーザー情報の表示 -->
                                <?php echo htmlspecialchars($name . '@user' . $member_id); ?></a>
                            </p>

                            <!-- メッセージの表示 -->
                            <p class="newline">
                                <?php echo htmlspecialchars($message); ?>
                            </p>

                            <!-- 投稿時間の表示 -->
                            <div class="time">
                                <small><?php echo htmlspecialchars($created); ?></small>

                                <!-- 自分の投稿であれば削除できる -->
                                <?php if ($_SESSION['user_id'] === $member_id) : ?>
                                    <a href="../Delete_community/delete.php?id=<?php echo htmlspecialchars($id); ?>" class="a" style="color: darkgray;"><i class="fa-solid fa-trash-can"></i></a>
                                <?php endif; ?>
                            </div>
                        </li>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <div class="right-menu">
            <h2>いまどうしてる？</h2>

            <!-- ニュースを表示 -->
            <div class="iframely-embed">
                <div class="iframely-responsive" style="padding-bottom: 56.25%; padding-top: 120px;"><a href="https://www.mbc.co.jp/news/article/2022070400057366.html" data-iframely-url="//iframely.net/y5CgeeE"></a></div>
            </div>

            <script async src="//iframely.net/embed.js" charset="utf-8"></script>

            <div class="whether">
                <!-- 天気情報の取得 -->
                <style>
                    .max_temp {
                        display: inline-block !important
                    }

                    .min_temp {
                        display: inline-block !important
                    }

                    .temp {
                        display: block !important
                    }

                    .rain_s {
                        display: block !important
                    }
                </style>
                <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/weather-icons/2.0.12/css/weather-icons.min.css">
                <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/weather-icons/2.0.12/css/weather-icons-wind.css">
                <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
                <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
                <script type="text/javascript">
                    weather_value = 7;
                    lat = 34.695084;
                    lon = 135.19783;
                    inputText1 = "兵庫県神戸市中央区";
                    search_add = "兵庫県神戸市中央区";
                </script>
                <script src="https://sitecreation.co.jp/wp-content/themes/emanon-premium-child/tpl/weather.js"></script>
                <link id="PageStyleSheet" rel="stylesheet" href="https://sitecreation.co.jp/wp-content/themes/emanon-premium-child/tpl/style.css">
                <div id="weather-wrapper">
                    <div id="weather1"></div>
                    <div id="weather2"></div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>