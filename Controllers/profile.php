<?php
/////
//プロフィールコントローラ-
////

//設定を読み込む
include_once '../config.php';
//便利な関数を読み込む
include_once '../util.php';

//ユーザーデータ操作モデルを読み込む
include_once '../Models/users.php';
//ツイート操作モデルを読み込む
include_once '../Models/tweets.php';

//--------
//ログインチェック
//--------
$user = getUserSession();
    if(!$user){
        //ログインしていない
        header('Location:' . HOME_URL . 'Controllers/sign-in.php');
        exit;
    }
//------
//ユーザー情報を変更
//-----
//ニックネームとユーザー名とメールアドレスが入力されている場合
if(isset($_POST['nickname'])&& isset($_POST['name'])&& isset($_POST['email'])){
$data =[
    'id' => $user['id'],
    'name' => $_POST['name'],
    'nickname' => $_POST['nickname'],
    'email' => $_POST['email'],
];
//パスワードが入力されていた場合->パスワード変更
if (isset($_POST['password']) && $_POST['password'] !== '') {
    // パスワード変更
    $data['password'] = $_POST['password'];
}

if (isset($_FILES['image']) && is_uploaded_file($_FILES['image']['tmp_name'])) {
    // 画像アップロード
    $data['image_name'] = uploadImage($user, $_FILES['image'], 'user');
}

// 更新実行
if (updateUser($data)) {
    // 成功したらセッションに保存
    $user = findUser($user['id']);
    saveUserSession($user);
    // リロード
    header('Location: ' . HOME_URL . 'Controllers/profile.php');
}
}


//---------
//表示するユーザーIDを取得（デフォルトはログインユーザー）
//--------
//URLにuser_idがある場合->それを対象ユーザーにする
$requested_user_id = $user['id'];
if(isset($_GET['user_id'])){
$requested_user_id = $_GET['user_id'];
}

//-------
//表示用の変数
//------
//ユーザー情報
$view_user = $user;
//プロフィール詳細を取得
$view_requested_user = findUser($requested_user_id, $user['id']);
//ツイート一覧
$view_tweets = findTweets($user, null,[$requested_user_id]);
//画面表示
include_once '../Views/profile.php';