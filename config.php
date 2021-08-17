<?php
// エラー表示あり
ini_set('display_errors', 1);
// 日本時間にする
date_default_timezone_set('Asia/Tokyo');
// URL/ディレクトリ設定
define('HOME_URL', '/TwitterClone/');
//データベースの接続情報
define('DB_HOST', 'localhost');
define('DB_USER','dbuser');
define('DB_PASSWORD', 'Hiro9140@');
define('DB_NAME','twitter_clone');