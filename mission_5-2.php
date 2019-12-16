<?php  
	//データベース作成
	$dsn = "mysql:dbname=**********;host=localhost";
	$user = "**********";
	$password = "**********";
	$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
	$sql = "CREATE TABLE IF NOT EXISTS mission_5"
	." ("
	. "id INT AUTO_INCREMENT PRIMARY KEY,"
	. "name char(32),"
	. "comment TEXT,"
	. "date_1 TEXT,"
	. "pass TEXT"
	.");";
	$stmt = $pdo->query($sql);
	
	$name_input = "";
	$comment_input = "";
	$number_input = "";
	$error_num = 0;
	
	//編集の際フォームに表示させる処理
	if (!empty($_POST["edit"]) && !empty($_POST["pass_3"])) {
		$edit_num = $_POST["edit"];
		$edit_pass = $_POST["pass_3"];
		$id_exist = 0;
		$sql = 'SELECT * FROM mission_5';
		$stmt = $pdo->query($sql);
		$results = $stmt->fetchAll();
		foreach ($results as $row){
			if ($row['id'] == $edit_num) {
			$id_exist = 1;
				if ($row['pass'] == $edit_pass) {
					$name_input = $row['name'];
					$comment_input = $row['comment'];
					$number_input = $row['id'];
				}
				else {
					$error_num = 6;
				}
			}
		}
		if ($id_exist == 0) {
			$error_num = 7;
		}
	}
?>

<html>
<head>
<meta http-equiv = "content-type" charset = "utf-8">
<title> mission_5</title>
</head>
<body>
<h2>　　雑談　　</h2>
　　[ 投稿フォーム ]
<form action = "mission_5-2.php" method = "POST">
	　<input type = "text" name = "name" placeholder = "名前" value = "<?php echo $name_input ?>" /> 
	<br />
	　<input type = "text" name = "comment" placeholder = "コメント" value = "<?php echo $comment_input ?>" />
	　<input type = "hidden" name = "editing" value = "<?php echo $number_input ?>" />
	<br />
	　<input type = "password" name = "pass_1" placeholder = "パスワード" />
	<input type = "submit" value = "送信" />
</form>
　　[ 削除フォーム ]
<form action = "mission_5-2.php" method = "POST">
	　<input type = "text" name = "delete" placeholder = "削除対象番号" />
	<br />
	　<input type = "password" name = "pass_2" placeholder = "パスワード" />
	<input type = "submit" value = "削除" />
</form>
　　[ 編集フォーム ]
<form action = "mission_5-2.php" method = "POST">
	　<input type = "text" name = "edit" placeholder = "編集対象番号" />
	<br />
	　<input type = "password" name = "pass_3" placeholder = "パスワード" />
	<input type = "submit" value = "編集" />
</form>
</body>
</html>

<?php
	
	//新規投稿または編集の処理\
	if (!empty($_POST["name"]) && !empty($_POST["comment"]) && !empty($_POST["pass_1"])) {
		
		//新規投稿の処理
		if (empty($_POST["editing"])) {
			$sql = $pdo -> prepare("INSERT INTO mission_5 (name, comment, date_1, pass) VALUES (:name, :comment, :date_1, :pass)");
			$sql -> bindParam(':name', $name, PDO::PARAM_STR);
			$sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
			$sql -> bindParam(':date_1', $date_1, PDO::PARAM_STR);
			$sql -> bindParam(':pass', $pass, PDO::PARAM_STR);
			$name = $_POST["name"];
			$comment = $_POST["comment"];
			$date_2 = date("Y/m/d G:i:s");
			$date_1 = $date_2;
			$pass = $_POST["pass_1"];
			$sql -> execute();
		}
		
		//編集投稿の処理
		elseif (!empty($_POST["editing"])) {
			$edit_num = $_POST["editing"];
			$edit_pass = $_POST["pass_1"];
			$name = $_POST["name"];
			$comment = $_POST["comment"];
			$date_1 = date("Y/m/d G:i:s");
			$sql = 'SELECT * FROM mission_5';
			$stmt = $pdo->query($sql);
			$results = $stmt->fetchAll();
			foreach ($results as $row) {
				if ($edit_num == $row['id']) {
					if ($edit_pass == $row['pass']) {
						$sql = 'update mission_5 set name=:name,comment=:comment,date_1=:date_1 where id=:id';
						$stmt = $pdo->prepare($sql);
						$stmt->bindParam(':name', $name, PDO::PARAM_STR);
						$stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
						$stmt->bindParam(':date_1', $date_1, PDO::PARAM_STR);
						$stmt->bindParam(':id', $edit_num, PDO::PARAM_INT);
						$stmt->execute();
					}
					else {
						$error_num = 6;
					}
				}
			}
		}
	}
	
	//削除の処理
	if (!empty($_POST["delete"]) && !empty($_POST["pass_2"])) {
		$delete_num = $_POST["delete"];
		$delete_pass = $_POST["pass_2"];
		$id_exist = 0;
		$sql = 'SELECT * FROM mission_5';
		$stmt = $pdo->query($sql);
		$results = $stmt->fetchAll();
		foreach ($results as $row){
			if ($row['id'] == $delete_num) {
			$id_exist = 1;
				if ($row['pass'] == $delete_pass) {
					$sql = 'delete from mission_5 where id=:id';
					$stmt = $pdo->prepare($sql);
					$stmt->bindParam(':id', $delete_num, PDO::PARAM_INT);
					$stmt->execute();
				}
				else {
					$error_num = 6;
				}
			}
		}
		if ($id_exist == 0) {
			$error_num = 7;
		}
	}
	
	//エラーメッセージの選択
	if (empty($_POST["name"]) && (!empty($_POST["comment"]) or !empty($_POST["pass_1"]))) {
		$error_num = 1;
	}
	elseif (empty($_POST["comment"]) && (!empty($_POST["name"]) or !empty($_POST["pass_1"]))) {
		$error_num = 2;
	}
	elseif (empty($_POST["pass_1"]) && !empty($_POST["name"]) && !empty($_POST["comment"])) {
		$error_num = 3;
	}
	elseif (empty($_POST["delete"]) && (!empty($_POST["pass_2"]))) {
		$error_num = 4;
	}
	elseif (empty($_POST["pass_2"]) && (!empty($_POST["delete"]))) {
		$error_num = 3;
	}
	elseif (empty($_POST["edit"]) && (!empty($_POST["pass_3"]))) {
		$error_num = 5;
	}
	elseif (empty($_POST["pass_3"]) && (!empty($_POST["edit"]))) {
		$error_num = 3;
	}

	//エラーメッセージの表示
	if ($error_num == 0) {
		echo "　　　入力してください";
	}
	elseif ($error_num == 1) {
		echo "　　　名前を入力してください";
	}
	elseif ($error_num == 2) {
		echo "　　　コメントを入力してください";
	}
	elseif ($error_num == 3) {
		echo "　　　パスワードを入力してください";
	}
	elseif ($error_num == 4) {
		echo "　　　削除対象番号を入力してください";
	}
	elseif ($error_num == 5) {
		echo "　　　編集対象番号を入力してください";
	}
	elseif ($error_num == 6) {
		echo "　　　パスワードが違います";
	}
	elseif ($error_num == 7) {
		echo "　　　対象番号が存在しません";
	}
	echo "<br>" . "<br>";
	echo "[ 投稿一覧 ]" . "<br>";
	
	//投稿の表示
	$sql = 'SELECT * FROM mission_5';
	$stmt = $pdo->query($sql);
	$results = $stmt->fetchAll();
	foreach ($results as $row) {
		echo $row['id'] . '　';
		echo 'name : ' . $row['name'] . '　';
		echo $row['date_1'] . '<br>';
		echo $row['comment'] . '<br>';
		echo "<hr>";
	}
?>