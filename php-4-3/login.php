<?php
session_start();

$dsn = 'mysql:dbname=zubkov;host=localhost;charset=utf8';
$user = 'zubkov';
$password = 'neto1579';
$infoText = '';

	try {
		$db = new PDO($dsn, $user, $password);
	} catch (PDOException $e) {
		echo 'Подключение не удалось: ' . $e->getMessage();
	}

	function getUsersList($db, $login)
	{
		$sqlSelect = "SELECT id, login, password FROM user WHERE login=?";
		$statement = $db->prepare($sqlSelect);
		$statement->execute([$login]);
		return $statement->fetch(PDO::FETCH_ASSOC);
	}

	/*Регистрация*/
	if (!empty($_POST['reg_submit'])) {
		if (!empty($_POST['login']) && !empty($_POST['password'])) {
			$login = $_POST['login'];
			$password = md5($_POST['password']);
			$usersArr = getUsersList($db, $login);

			if (empty($usersArr)) {
				$sqlAdd = "INSERT INTO user (login, password) VALUES (?, ?)";
				$statement = $db->prepare($sqlAdd);
				$statement->execute([$login, $password]);
				$usersArr = getUsersList($db, $login);
				$_SESSION['login'] = $login;
				$_SESSION['login_id'] = $usersArr['id'];
				header('Location: index.php');
			} else {
				$infoText = 'Такой пользователь уже есть в базе. Пожалуйста, введите логин другой';
			}
		}
		if (empty($_POST['login'])) {
			$infoText = 'Вы не ввели логин. Заполните форму заново';
		} elseif (empty($_POST['password'])) {
			$infoText = 'Вы не ввели пароль. Заполните форму заново';
		}
	}
	
	if (!empty($_POST['auth_submit'])) {
		if (!empty($_POST['login']) && !empty($_POST['password'])) {
			$login = $_POST['login'];
			$password = md5($_POST['password']);
			$usersArr = getUsersList($db, $login);
			if (!empty($usersArr) && $usersArr['password'] === $password) {
				$_SESSION['login'] = $login;
				$_SESSION['login_id'] = $usersArr['id'];
				header('Location: index.php');
			} else {
				$infoText = 'Вы ввели неверный логин или пароль';
			}
		} else {
			$infoText = 'Вы ввели не все данные для входа';
		}
	}
	?>

	<!doctype html>
	<html lang="ru">
	<head>
		<meta charset="UTF-8">
		<meta http-equiv="X-UA-Compatible" content="ie=edge">
		<title>Панель авторизации</title>
	</head>
	<body>
					<h1>Панель авторизации</h1>

					<form method="post">
						<input name="login" type="text" placeholder="Логин">
						<input name="password" type="password" placeholder="Пароль">
						<input name="auth_submit" type="submit" value="Войти">
						<input name="reg_submit" type="submit" value="Регистрация">
					</form>

					<p style="color: red"><?=$infoText?></p>
				</body>
				</html>