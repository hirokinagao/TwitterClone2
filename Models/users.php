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
    $query = 'INSERT INTO users (email, name, nickname, password) VALUES (?, ?, ?, ?)';
    $statement = $mysqli->prepare($query);
    // パスワードをハッシュ値に変換
    $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
    // ? の部分にセットする内容
    // 第一引数のsは変数の型を指定(s=string)
    $statement->bind_param('ssss', $data['email'], $data['name'], $data['nickname'], $data['password']);
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
/**
 * ユーザーを更新
 *
 * @param array $data
 * @return bool
 */
function updateUser(array $data)
{
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    // 接続チェック
    if ($mysqli->connect_errno) {
        echo 'MySQLの接続に失敗しました。：' . $mysqli->connect_error . "\n";
        exit;
    }
    // 更新日時
    $data['updated_at'] = date('Y-m-d H:i:s');
    if (isset($data['password'])) {
        // パスワードをハッシュ値に変換
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
    }
    // 更新のSQLを作成
    // SET句のカラムを準備
    $set_columns = [];
    foreach ([
        'name', 'nickname', 'email', 'password', 'image_name', 'updated_at'
    ] as $column) {
        // 入力があれば、更新の対象にする
        if (isset($data[$column]) && $data[$column] !== '') {
            $set_columns[] = $column . ' = "' . $mysqli->real_escape_string($data[$column]) . '"';
        }
    }
    $query = 'UPDATE users SET ' . join(',', $set_columns);
    $query .= ' WHERE id = "' . $mysqli->real_escape_string($data['id']) . '"';
    // SQLを実行
    $response = $mysqli->query($query);
    // 結果が失敗の場合、エラー表示
    if ($response === false) {
        echo 'エラーメッセージ：' . $mysqli->error . "\n";
    }
    $mysqli->close();
    return $response;
}
/**
 * ユーザー情報取得：ログインチェック
 *
 * @param string $email
 * @param string $password
 * @return array|false
 */
function findUserAndCheckPassword(string $email, string $password)
{
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    // 接続チェック
    if ($mysqli->connect_errno) {
        echo 'MySQLの接続に失敗しました。：' . $mysqli->connect_error . "\n";
        exit;
    }
    // 入力値をエスケープ
    $email = $mysqli->real_escape_string($email);
    // クエリを作成
    // - 外部からのリクエストは何が入ってくるかわからないので、必ず、エスケープしたものをクオートで囲む
    $query = 'SELECT * FROM users WHERE email = "' . $email . '"';
    // SQL実行
    $result = $mysqli->query($query);
    if (!$result) {
        // MySQL処理中にエラー発生
        echo 'エラーメッセージ：' . $mysqli->error . "\n";
        $mysqli->close();
        return false;
    }
    // ユーザー情報を取得
    $user = $result->fetch_array(MYSQLI_ASSOC);
    if (!$user) {
        // ユーザーが存在しない
        $mysqli->close();
        return false;
    }
    // パスワードチェック
    if (!password_verify($password, $user['password'])) {
        // パスワード不一致
        $mysqli->close();
        return false;
    }
    $mysqli->close();
    return $user;
}
/**
 * ユーザーを取得
 *
 * @param integer $user_id
 * @param integer $login_user_id
 * @param string $email 未実装
 * @param string $name 未実装
 * @return array|false
 */
function findUser(int $user_id = null, int $login_user_id = null, string $email = null, string $name = null)
{
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    // 接続チェック
    if ($mysqli->connect_errno) {
        echo 'MySQLの接続に失敗しました。：' . $mysqli->connect_error . "\n";
        exit;
    }
    // エスケープ
    $user_id = $mysqli->real_escape_string($user_id);
    $login_user_id = $mysqli->real_escape_string($login_user_id);
    // 検索のSQLを作成
    $query = <<<SQL
        SELECT
            U.id,
            U.name,
            U.nickname,
            U.email,
            U.image_name,
            -- フォロー中の数
            (SELECT COUNT(1) FROM follows WHERE status = 'active' AND follow_user_id = U.id) AS follow_user_count,
            -- フォローワーの数
            (SELECT COUNT(1) FROM follows WHERe status = 'active' AND followed_user_id = U.id) AS followed_user_count,
            -- ログインユーザーがフォローしている場合、フォローIDが入る
            F.id AS follow_id
        FROM
            users AS U
            -- ログインしているユーザーがフォローしているかの判定のため
            LEFT JOIN
                follows AS F ON F.status = 'active' AND F.followed_user_id = '$user_id' AND F.follow_user_id = '$login_user_id'
        WHERE
            U.status = 'active' AND U.id = '$user_id'
    SQL;
    // SQLを実行
    if ($result = $mysqli->query($query)) {
        // データを配列で返却
        $response = $result->fetch_array(MYSQLI_ASSOC);
    } else {
        // 失敗
        $response = false;
        echo 'エラーメッセージ：' . $mysqli->error . "\n";
    }
    $mysqli->close();
    return $response;
}