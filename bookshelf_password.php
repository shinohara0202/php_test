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
                <nav>
                    <a href="./bookshelf_password.php"><img src="./images/icon_plus.png" alt=""> パスワード登録</a>
                </nav>
            </div>
        </header>
        <div id="wrapper">
            <div id="main">
                <form action="bookshelf_index.php" method="post" class="form_password">
                    <div class="password">
                        <input type="password" name="add_password" placeholder="パスワードを入力" required>
                    </div>
                    <div class="password_submit">
                        <input type="submit" name="submit_password" value="登録">
                    </div>
                </form>
            </div>
        </div>
        <footer>
            <small>© 2019 Bookshelf.</small>
        </footer>
    </body>
</html>