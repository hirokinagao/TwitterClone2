<?php
///////////////////////////////////////
// ユーザーデータを処理
///////////////////////////////////////
 
/**
 * ユーザーを作成
 *
 * @param array $data
 * @return bool
 */
function createUser(array $data)
{
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    // 接続チェック
    if ($mysqli->connect_errno) {
        echo 'MySQLの接続に失敗しました。：' . $mysqli->connect_error . "\n";
        exit;
    }
 
    // 新規登録のSQLを作成
    $query = 'insert into users(nickname, name, email, password) values(?, ?, ?, ?)';
    $statement = $mysqli->prepare($query);
 
    // パスワードをハッシュ値に変換
    $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
 
    // ? の部分にセットする内容
    // 第一引数のsは変数の型を指定(s=string)
    $statement->bind_param('ssss', $data['nickname'], $data['name'], $data['email'], $data['password']);
 
    // 処理を実行
    $response = $statement->execute();
    if ($response === false) {
        echo 'エラーメッセージ：' . $mysqli->error . "\n";
    }
 
    // 接続を解放
    $statement->close();
    $mysqli->close();
 
    return $response;
}
