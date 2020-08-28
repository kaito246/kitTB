<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>mission_5-1</title>

<!--メインファイルと違い右側にもborder入れてる-->

    <style>
    .left{float: left;
          width: 48%;
          border-right: 2px solid #ccc;
    } 

    .right{float: right;
           width: 50%;
           border-left: 2px solid #ccc;
           padding :10px;
    }    
    </style>

</head>
<body>

    <?php
        error_reporting(E_ALL & ~E_NOTICE); //Noticeを表示させない

        //DB接続設定
        $dsn = 'データベース名';
        $user = 'ユーザー名';
        $password = 'パスワード';
        $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

        
        // 色々定義する
        $name = $_POST["name"];
        $com = $_POST["comment"];
        $pass = $_POST["pass"];
        $su = $_POST["su"];
        $dnum = $_POST["dnum"];
        $dpass = $_POST["dpass"];
        $bm = $_POST["bm"];
        $enum = $_POST["enum"];
        $epass = $_POST["epass"];
        $it = $_POST["it"];
        $ehnum = $_POST["ehnum"];
        $date = date("Y/m/d H:i:s");


        if(isset($su)){//送信ボタンが押されたとき
            
            if($ehnum == NULL){//新規モードのとき
                if($name != NULL && $com != NULL && $pass != NULL){//全て記入できているとき
                    //データを入力
                    $sql = $pdo -> prepare("INSERT INTO m5_1 (name, comment, date, password) VALUES (:name, :comment, :date, :password)");
	                $sql -> bindParam(':name', $name, PDO::PARAM_STR);
                    $sql -> bindParam(':comment', $com, PDO::PARAM_STR); 
                    $sql -> bindParam(':date', $date, PDO::PARAM_STR);
                    $sql -> bindParam(':password', $pass, PDO::PARAM_STR);
                    $sql -> execute();
                    $suc = "投稿成功";
                }else{$miss = "入力ミスがあります";} //もう少し丁寧に分岐させるかも           
            
            }else{//編集モードのとき
                if($name != NULL && $com != NULL && $pass != NULL){//全て記入されているとき
                    //データを編集
                    $id = $ehnum;
                    $stmt = $pdo -> prepare("UPDATE m5_1 SET name=:name,comment=:comment,date=:date,password=:password WHERE id=:id");
                    $stmt -> bindParam(':name', $name, PDO::PARAM_STR);
                    $stmt -> bindParam(':comment', $com, PDO::PARAM_STR);
                    $stmt -> bindParam(':date', $date, PDO::PARAM_STR);
                    $stmt -> bindParam(':password', $pass, PDO::PARAM_STR);
                    $stmt -> bindParam(':id', $id, PDO::PARAM_INT);
                    $stmt -> execute();
                    $esuc = "編集成功";
                }else{$edmiss = "入力ミスがあります";}//もう少し丁寧に分岐するかも
            }
        }

        //削除
        if(isset($bm)){//削除ボタンが押されたとき           
            if($dnum != NULL && $dpass != NULL){//完璧に入力できているとき
                $sql = 'SELECT * FROM m5_1';
	            $stmt = $pdo->query($sql);
	            $results = $stmt->fetchAll();
	            foreach ($results as $row){
                    if($dnum == $row['id'] && $dpass == $row['password']){
                        $sql = 'delete from m5_1 where id=:id';
                        $stmt = $pdo->prepare($sql);
                        $stmt->bindParam(':id', $dnum, PDO::PARAM_INT);
                        $stmt->execute(); 
                        $dsuc = "削除成功";
                    }elseif($dnum == $row['id'] && $dpass != $row['password']){
                        $dpmiss = "パスワードが違います";
                    }  
	            }          
            }else{$dmiss = "入力ミスがあります";}//もっと細かくエラー表示するかも
        }

        //編集
        if(isset($it)){//編集ボタンが押されたとき
            if($enum != NULL && $epass != NULL){//入力が完璧なら
                $sql = 'SELECT * FROM m5_1';
                $stmt = $pdo->query($sql);
                $results = $stmt->fetchAll();
                foreach($results as $row){
                    if($enum == $row['id'] && $epass == $row['password']){
                        $editnum = $enum;
                        $editname = $row['name'];
                        $editcom = $row['comment'];
                        $editpass = $row['password'];
                        $editsuc = "投稿フォームへ移動";
                    }elseif($enum == $row['id'] && $epass != $row['password']){
                        $epmiss = "パスワードが違います";
                    }
                }
            }else{$emiss = "入力ミスがあります";}//もっと細かくエラー表示するかも
        }       
    ?>

    <h1>Mission5</h1>
    <hr>

    <div class = "left">
    <h2>投稿入力</h2>
    <form action = "" method = "post">
        お名前<br>
        <input type = "text" name = "name" placeholder = "名前を記入" value = "<?php echo $editname ?>"><br><br>
        コメント<br>
        <input type = "text" name = "comment" placeholder = "コメントを記入" value = "<?php echo $editcom ?>"><br><br>
        パスワード<br>
        <input type = "text" name = "pass" placeholder = "パスワードを記入" value = "<?php echo $editpass ?>"><br><br>
        <input type = "hidden" name = "ehnum" value = "<?php echo $editnum ?>">
        <input type = "submit" name = "su" value = "送信"> <?php echo $suc; echo $miss; echo $esuc; echo $edmiss; ?>
        <hr>
    </form>

    <h2>投稿削除</h2>
    <form action = "" method = "post">
        削除したい投稿番号<br>
        <input type = "number" name = "dnum" placeholder = "削除したい投稿番号"><br><br>
        投稿のパスワード<br>
        <input type = "text" name = "dpass" placeholder = "パスワードを記入"><br><br>
        <input type = "submit" name = "bm" value = "削除"><?php echo $dsuc; echo $dpmiss; echo $dmiss; ?>
        <hr>
    </form>

    <h2>投稿編集</h2>
    <form action = "" method = "post">
        編集したい投稿番号<br>
        <input type = "number" name = "enum" placeholder = "編集したい投稿番号"><br><br>
        投稿のパスワード<br>
        <input type = "text" name = "epass" placeholder = "パスワードを記入"><br><br>
        <input type = "submit" name = "it" value = "編集"><?php echo $editsuc; echo $epmiss; echo $emiss; ?>
        <hr>
    </form>
    </div>

    <div class ="right">
    <h2>投稿表示</h2>
    <?php
        //入力したデータを抽出して表示
        $sql = 'SELECT * FROM m5_1';
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
        foreach ($results as $row){
            echo $row['id'].'　'; //$rowの中にはテーブルのカラム名が入る
            echo $row['name'].'　';
            echo $row['comment'].'　';
            echo $row['date'].'<br>';
            echo "<hr>";
        }
    ?>
    </div>
</body>
</html>