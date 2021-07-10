<?php
// 設定を読み込む
include('../config.php');
// 便利な関数を読み込む
include('../util.php');
?>
<!DOCTYPE html>
<html lang="ja">
 
<head>
  
<?php include_once('../Views/common/head.php'); ?>
 
    <title>ログイン画面/ Twitterクローン</title>
    <meta name="description" content="ログイン画面です">
</head>
<body class="signup text-center">
    <main class="form-signup">
       <form action="sign-in.php" method="post">
         <img src="/TwitterClone/Views/img/logo-white.svg" alt="" class="logo-white">
         <h1>TwitterCloneにログイン</h1>
         
         <input type="email" class="form-control" name="email" placeholder="メールアドレス"  reguired autofoucus>
         <input type="password" class="form-control" name="password" placeholder="パスワード"  reguired>
         <button class="w-100 btn btn-lg"type="submit">ログイン</button>
         <p class="mt-3 mb-2"><a href="sign-up.php">会員登録する</a></p>
         <p class="mt-2 mb-3 text-muted">&copy;2021</p>
       </form>  
    </main>
    <?php  include_once('../Views/common/foot.php'); ?>
    
</body>
</html>