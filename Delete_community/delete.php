<?php
// sessionスタート
session_start();

// ログインしている場合
if (isset($_SESSION['id'])) {
    $id = $_SESSION['user_id'];
    $name = $_SESSION['user_name'];
} else {
    // ログインしていない場合、ログインページへ戻す
    header('Location: login.php');
    exit();
}

// functionの呼びだし
$db = new mysqli('localhost', 'root', 'root', 'kadai_db');


$post_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

// 削除対策
$stmt = $db->prepare('DELETE FROM posts WHERE id=? AND member_id=? LIMIT 1');
$stmt->bind_param('ii', $post_id, $id);
$stmt->execute();

header('Location: ../community_home/home.php');
exit();
