<?php
//1.ユーザ登録画面から送信された情報をPHPで受け取る
//2.ユーザ登録画面で送信された情報をチェックする(バリデーションチェック)
    //2.1○post送信されているか
    //2.2○各フォームの中身が入力されているか
    //2.3○emailフォームemail形式かどうか
    //2.4○パスワードと再入力フォームは同じ値かどうか
    //2.5○パスワードと再入力フォームは半角英数字かどうか
    //2.6○パスワード再入力フォームは6文字以上か
//3.バリデーションチェックに問題があればエラーを表示する
//4.バリデーションチェックに問題なければDBへユーザ情報を保存
//5.DB登録後にマイページに遷移する

//追加したい機能
//1.emailの重複チェック(エラーだった場合に違うメールアドレスを登録するようにしたい)の追加




//エラーの設定
error_reporting(E_ALL);
ini_set('display_errors','On');//画面にエラーを表示させるか

//1.post送信されていた場合
if(!empty($_POST)){

  //2.バリデーションチェック

  //エラーメッセージ
  define('MSG01', '※入力必須です');
  define('MSG02', '※Emailの形式で入力してください');
  define('MSG03', '※パスワード(再入力)が違います');
  define('MSG04', '※半角英数字で入力してください');
  define('MSG05', '6文字以上で入力してください');

  //配列error_msgを用意
  $error_msg = array();

  //2.2各フォームの中身が入力されているか
  if(empty($_POST['email'])){
    $error_msg['email'] = MSG01 ;
  }

  if(empty($_POST['pass'])){
    $error_msg['pass'] = MSG01 ;
  }

  if(empty($_POST['pass_retype'])){
    $error_msg['pass_retype'] = MSG01 ;
  }

  //エラーでない場合
  if(empty($error_msg)){
    //変数にユーザ情報を入力
    $email = $_POST['email'];
    $pass = $_POST['pass'];
    $pass_retype = $_POST['pass_retype'];

    //2.3emailフォームemail形式かどうか
    $pattern = "/^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/";//バリデーション(emil)
    if(!preg_match($pattern, $email)){
      $error_msg['email'] = MSG02;
    }else

    //2.4パスワードと再入力フォームは同じ値かどうか
    if($pass !== $pass_retype){
      $error_msg['pass'] = MSG03;
    }

    //2.5○パスワードと再入力フォームは半角英数字かどうか
    $pattern = "/^[a-zA-Z0-9]+$/";//バリデーション(半角英数字)
    if(!preg_match($pattern, $pass)){
      $error_msg['pass'] = MSG04;
    }

    //2.6○パスワード再入力フォームは6文字以上か
    if(mb_strlen($pass) < 6){
      $error_msg['pass'] = MSG05;
    }
  }

  if(empty($error_msg)){
    //4.バリデーションチェックに問題なければDBへユーザ情報を保存

    $dsn = 'mysql:dbname=php_op_01;host=localhost;charset=utf8';//DB情報
    $user = 'root';
    $password = 'root';
    $options = array(
      // SQL実行失敗時に例外をスロー
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      // デフォルトフェッチモードを連想配列形式に設定
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      // バッファードクエリを使う(一度に結果セットをすべて取得し、サーバー負荷を軽減)
      // SELECTで得た結果に対してもrowCountメソッドを使えるようにする
      PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
    );

    //PDOオブジェクト作成
    $dbh = new PDO($dsn, $user, $password, $options);

    //クエリ(sql文)
    $stmt = $dbh -> prepare('INSERT INTO users(email,pass,login_time) VALUES(:email,:pass,:login_time)');

    //プレースホルダーに値をセットしてSQLを実行
    $stmt->execute(array(':email' => $email,':pass' => $pass,':login_time' => date('Y-m-d H:i:s')));
  }

}

?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>情報登録</title>
    <!-- CSSの読み込み -->
    <link rel="stylesheet" href="style.css">
  </head>

  <body>
    <h1 class="heading-page">情報登録</h1>

    <form method="post">
      <span class="error_msg"><?PHP if(!empty($error_msg['email'])) echo $error_msg['email']; ?></span>
      <input type="text" name="email" placeholder="email">
      <span class="error_msg"><?PHP if(!empty($error_msg['pass'])) echo $error_msg['pass']; ?></span>
      <input type="password" name="pass" placeholder="パスワード">
      <span class="error_msg"><?PHP if(!empty($error_msg['pass_retype'])) echo $error_msg['pass_retype']; ?></span>
      <input type="password" name="pass_retype" placeholder="パスワード(再入力)">
      <input type="submit" value="送信">

    </form>

  </body>
</html>
