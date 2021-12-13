<?php
	// Als de signup form gesubmit is
	if (isset($_POST["submit"])) {
		$name = $_POST["fullname"];
		$email = $_POST["email"];
		$username = $_POST["username"];
		$pwd = $_POST["pwd"];
		$pwdRepeat = $_POST["pwdrepeat"];

		require_once 'dbhandler-inc.php';
		require_once 'functions-inc.php';

		//Als 1 van deze functies true returnt stuur dan de user naar 1 van deze adressen
		if (emptyInputSignup($name, $email, $username, $pwd, $pwdRepeat) !== false) {
			header("location: ../signup.php?error=emptyinput");
			exit();
		}

		if (invalidUsername($username) !== false) {
			header("location: ../signup.php?error=invalidusername");
			exit();
		}

		if (invalidEmail($email) !== false) {
			header("location: ../signup.php?error=invalidemail");
			exit();
		}

		if (pwdInvalid($pwd) !== false) {
			header("location: ../signup.php?error=pwdinvalid");
			exit();
		}

		if (pwdsDontMatch($pwd, $pwdRepeat) !== false) {
			header("location: ../signup.php?error=passwordsdontmatch");
			exit();
		}

		if (usernameOrEmailExists($conn, $username, $email) !== false) {
			header("location: ../signup.php?error=usernametaken");
			exit();
		}

		createUser($conn, $name, $email, $username, $pwd);
	}
	else {
		// Als de user de url gewoon ingetypt heeft, stuur hem terug naar index.php
		header("location: ../index.php");
		exit();
	}
