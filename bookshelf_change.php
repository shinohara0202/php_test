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


    //sqlからurlとtitleを取得する処理
    if(array_key_exists('submit_book_change', $_POST)){
        $id = $_POST["book_id"];
        $sql = " SELECT * FROM books where id=$id ";
        $result = mysqli_query($database, $sql);        
        $record = mysqli_fetch_assoc($result);
    }
    
    $id = $record["id"];
    $image_url = $record["image_url"];
    $title = $record["title"];      
    
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
            <div id="header">
                <div id="logo">
                    <a href="./bookshelf_index.php"><img src="./images/logo.png" alt="Bookshelf"></a>
                </div>
            </div>
        </header>
        
        <div id="wrapper">
            <div id="main">
                <div class="select_book">
                    
                    <div class="book_image">
                        <img src="<?php print h($image_url) ?>">
                    </div>
                    <div class="book_title">
                        <?php print h($title) ?>
                    </div>
                    
                </div>    

                <form action="bookshelf_index.php" method="post" class="form_book" enctype="multipart/form-data">
                    <input type="hidden" name="book_id" value=<?php print h($id) ?> />
                    <div class="book_title">
                        <input type="text" name="change_book_title" placeholder="書籍タイトルを入力">
                    </div>
                    <div class="book_image">
                        <input type="file" name="change_book_image">
                    </div>
                    <div class="password">
                        <input type="password" name="confirm_password" placeholder="パスワードを入力" required>
                    </div>
                    <div class="book_submit">
                        <input type="submit" name="change_add_book" value="修正">
                    </div>
                </form>
            </div>
        </div>
        
        <footer>
            <small>c 2019 Bookshelf.</small>
        </footer>
    </body>
</html>