<?php

    function h($str) {
        return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
    }

    // MySQLサーバ接続に必要な値を変数に代入
    $host = 'localhost';
    $username = 'dbuser';
    $password = 'dbpass';
    $db_name = 'bookshelf';

    // 変数を設定して、MySQLサーバに接続
    $database = mysqli_connect($host, $username, $password, $db_name);

    // 接続を確認し、接続できていない場合にはエラーを出力して終了する
    if ($database == false) {
        die('Connect Error (' . mysqli_connect_errno() . ') ' . mysqli_connect_error());
    }

    // MySQL に utf8 で接続するための設定をする
    $charset = 'utf8';
    mysqli_set_charset($database, $charset);

    // ここにMySQLを使ったなんらかの処理を書く
    

    //入力されたパスワードをDBに登録する
    if(array_key_exists("add_password",$_POST)){
        $sql = "update passtable set password=? where id=1";
        $statement = mysqli_prepare($database, $sql);
        mysqli_stmt_bind_param($statement, 's', $_POST['add_password']);
        mysqli_stmt_execute($statement);
        mysqli_stmt_close($statement);
    }
    
    //DBのパスワードを呼び出して代入する
    $sql = "SELECT password FROM passtable where id=1" ;
    $pass_result = mysqli_query($database, $sql);
    $pass_record = mysqli_fetch_assoc($pass_result);
    $password = $pass_record["password"];

   //パスワードの照合結果が合っていたら下記を処理
   
     //change.phpから受け取って、mysqlへ反映させる
    if(array_key_exists("change_add_book",$_POST)){
        if(array_key_exists("confirm_password",$_POST)){
            if($password == $_POST["confirm_password"]){
               $file_name = $_FILES['change_book_image']['name'];
               $image_path = './uploads/' . $file_name;
               move_uploaded_file($_FILES['change_book_image']['tmp_name'], $image_path);        
                
                $id = $_POST["book_id"];
                $sql = "UPDATE books SET title=?,image_url=? where id=$id";
                $statement = mysqli_prepare($database,$sql);
                mysqli_stmt_bind_param($statement, 'ss', $_POST["change_book_title"],$image_path);
                mysqli_stmt_execute($statement);
                mysqli_stmt_close($statement);        
            }else{
                $alert = "パスワードが違う為、変更できませんでした"; 
            }
        
        }
    }
    

        // bookshelf_form.phpから送られてくる書籍データの登録
         if (array_key_exists('submit_add_book', $_POST)) {
              if(array_key_exists("confirm_password",$_POST)){
                   if($password == $_POST["confirm_password"]){
                       
                       // まずは送られてきた画像をuploadsフォルダに移動させる
                       $file_name = $_FILES['add_book_image']['name'];
                       $image_path = './uploads/' . $file_name;
                       move_uploaded_file($_FILES['add_book_image']['tmp_name'], $image_path);
            
                       // データベースに書籍を新規登録する
                       $sql = 'INSERT INTO books (title, image_url, status) VALUES(?, ?, "unread")';
                       $statement = mysqli_prepare($database, $sql);
                       mysqli_stmt_bind_param($statement, 'ss', $_POST['add_book_title'], $image_path);
                       mysqli_stmt_execute($statement);
                       mysqli_stmt_close($statement);
                    } else{
                            $alert = "パスワードが違う為、登録できませんでした";
                    }
                }
   }
   
    
    
    // ステータス変更の処理
    if (array_key_exists('submit_book_unread', $_POST)) {
        // 未読へ変更
        $sql = 'UPDATE books SET status="unread" WHERE id=?';        // 実行するSQLを作成
        $statement = mysqli_prepare($database, $sql);                // セキュリティ対策をする
        mysqli_stmt_bind_param($statement, 'i', $_POST['book_id']);  // id=?の?の部分に代入する
        mysqli_stmt_execute($statement);                             // SQL文を実行する
        mysqli_stmt_close($statement);                               // SQL文を破棄する
    }
    elseif (array_key_exists('submit_book_reading', $_POST)) {
        // 読中へ変更
        $sql = 'UPDATE books SET status="reading" WHERE id=?';
        $statement = mysqli_prepare($database, $sql);
        mysqli_stmt_bind_param($statement, 'i', $_POST['book_id']);
        mysqli_stmt_execute($statement);
        mysqli_stmt_close($statement);
    }
    elseif (array_key_exists('submit_book_finished', $_POST)) {
        // 読了へ変更
        $sql = 'UPDATE books SET status="finished" WHERE id=?';
        $statement = mysqli_prepare($database, $sql);
        mysqli_stmt_bind_param($statement, 'i', $_POST['book_id']);
        mysqli_stmt_execute($statement);
        mysqli_stmt_close($statement);
    }
    elseif (array_key_exists('submit_book_hold', $_POST)) {
        // 読了へ変更
        $sql = 'UPDATE books SET status="hold" WHERE id=?';
        $statement = mysqli_prepare($database, $sql);
        mysqli_stmt_bind_param($statement, 'i', $_POST['book_id']);
        mysqli_stmt_execute($statement);
        mysqli_stmt_close($statement);
    }
    
    //削除する処理
    if (array_key_exists('submit_book_delete', $_POST)) {  //もし$_POSTの中にデリートのkeyがあったら
       $sql = "DELETE FROM  books WHERE id=?"; //idは一旦dummyにしてデリートする
       $statement = mysqli_prepare($database, $sql); //セキュリティ対策
       mysqli_stmt_bind_param($statement, 'i', $_POST['book_id']); //id=?のところに代入
       mysqli_stmt_execute($statement); //実行
       mysqli_stmt_close($statement); //破棄
    }
    
    
    //未読数のカウント
    $sql = 'SELECT COUNT(*) as count FROM books WHERE status = "unread"';
    $result = mysqli_query($database,$sql);
    $record = mysqli_fetch_assoc($result);
    $count_unread = $record['count'];
    // 読中数のカウント
    $sql = 'SELECT COUNT(*) as count FROM books where status = "reading"';
    $result = mysqli_query($database, $sql);
    $record = mysqli_fetch_assoc($result);
    $count_reading = $record['count'];
    // 読了数のカウント
    $sql = 'SELECT COUNT(*) as count FROM books where status = "finished"';
    $result = mysqli_query($database, $sql);
    $record = mysqli_fetch_assoc($result);
    $count_finished = $record['count'];
    // 保留数のカウント
    $sql = 'SELECT COUNT(*) as count FROM books where status = "hold"';
    $result = mysqli_query($database, $sql);
    $record = mysqli_fetch_assoc($result);
    $count_hold = $record['count'];
    
    
    // どのボタンを押したか（どのステータスで絞り込みをするか）を判定し、SELECT文を変更する
    if (array_key_exists('submit_only_unread', $_POST)) {
        // 未読ステータスの書籍だけを取得する
        $sql = 'SELECT * FROM books WHERE status="unread" ORDER BY created_at DESC';
    }
    elseif (array_key_exists('submit_only_reading', $_POST)) {
        // 読中ステータスの書籍だけを取得する
        $sql = 'SELECT * FROM books WHERE status="reading" ORDER BY created_at DESC';
    }
    elseif (array_key_exists('submit_only_finished', $_POST)) {
        // 読了ステータスの書籍だけを取得する
        $sql = 'SELECT * FROM books WHERE status="finished" ORDER BY created_at DESC';
    }
    elseif (array_key_exists('submit_only_hold', $_POST)) {
        // 保留ステータスの書籍だけを取得する
        $sql = 'SELECT * FROM books WHERE status="hold" ORDER BY created_at DESC';
    }
    else {
        // 登録されている書籍を全て取得する
        $sql = 'SELECT * FROM books ORDER BY created_at DESC';
    }

    // いずれかの $sql を実行して $result に代入する
    $result = mysqli_query($database, $sql);

    // MySQLを使った処理が終わると、接続は不要なので切断する
    mysqli_close($database);
?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <title>Bookshelf | カンタン！あなたのオンライン本棚</title>
        <link rel="stylesheet" href="bookshelf.css">
    </head>
    <body>
        <header>
            <div class="alert">
                <?php print $alert ?>
            </div>
            <div id="header">
                <div id="logo">
                    <a href="./bookshelf_index.php"><img src="./images/logo.png" alt="Bookshelf"></a>
                </div>
                <nav>
                    <a href="./bookshelf_form.php"><img src="./images/icon_plus.png" alt=""> 書籍登録</a>
                </nav>
            </div>
        </header>
        <div id="cover">
            <h1 id="cover_title">カンタン！あなたのオンライン本棚</h1>
            <form action="bookshelf_index.php" method="post">
   
                <div class="book_status unread active">
                    <input type="submit" name="submit_only_unread" value="未読"><br>
                    <div class="book_count"><?php echo h($count_unread) ?></div>
                </div>
                <div class="book_status reading active">
                    <input type="submit" name="submit_only_reading" value="読中"><br>
                    <div class="book_count"><?php echo h($count_reading) ?></div>
                </div>
                <div class="book_status finished active">
                    <input type="submit" name="submit_only_finished" value="読了"><br>
                    <div class="book_count"><?php echo h($count_finished) ?></div>
                </div>
                <div class="book_status hold active">
                    <input type="submit" name="submit_only_hold" value="保留"><br>
                    <div class="book_count"><?php echo h($count_hold) ?></div>
                </div>
            </form>
        </div>
        <div class="wrapper">
            <div id="main">
                <div id="book_list">
<?php             if($result){
                   while($record = mysqli_fetch_assoc($result)) {
                            $id = $record['id'];
                            $title = $record['title'];
                            $image_url = $record['image_url'];
                            $status = $record['status'];                       
                            $created_at = $record["created_at"];
                            $update_at = $record["update_at"];
                   
                   


?>
                    <div class="book_item">
                        <div class="book_image 
<?php                    if($status == "unread") print "unread_color";
                         elseif($status == "reading") print "reading_color";
                         elseif($status == "finished") print "finished_color";
                         elseif($status == "hold") print "hold_color";
?>
                            ">
                            <img src="<?php echo h($image_url); ?>" alt="">
                        </div>
                        <div class="book_detail">
                            <div class="book_title">
                                <?php echo h($title); ?>
                            </div>
                            <div class="book_date">
                                <p>登録:<?php print h($created_at) ?></p>
                                <p>最終更新:<?php print h($update_at) ?></p>
                            </div>

                            <form action="bookshelf_index.php" method="post">
                                <input type="hidden" name="book_id" value="<?php print h($id) ?>" /> 
                                <div class="book_status unread  <?php if ($status == "unread") print "active"; ?>">
                                    <input type="submit" name="submit_book_unread" value="未読">
                                </div>
                                <div class="book_status reading <?php if ($status == "reading") print "active"; ?>">
                                    <input type="submit" name="submit_book_reading" value="読中">
                                </div>
                                    
                                <div class="book_status finished <?php if ($status == "finished") print "active"; ?>">
                                    <input type="submit" name="submit_book_finished" value="読了">
                                </div>
                                <div class="book_status hold <?php if ($status == "hold") print "active"; ?>">
                                    <input type="submit" name="submit_book_hold" value="保留">
                                </div>
                            </form>
                            <form action="bookshelf_change.php" method="post">
                                <input type="hidden" name="book_id" value="<?php echo h($id) ?>" />
                                <div class="book_change">
                                    <input type="submit" name="submit_book_change" value="書籍情報を変更する" />
                                </div>
                            </form>
                            <form action="bookshelf_index.php" method="post">
                                <input type="hidden" name="book_id" value="<?php echo h($id) ?>" />
                                <div class="book_delete">
                                  <input type="submit" name="submit_book_delete" value="削除する"><img src="./images/icon_trash.png" alt="icon trash">
                                </div>
                            </form>
                        </div>
                    </div>
<?php
                        }
                        mysqli_free_result($result);
                    }
?>
                </div>
            </div>
        </div>
        <footer>
            <small>c 2019 Bookshelf.</small>
        </footer>
    </body>
</html>