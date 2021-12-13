<?php

// Als de login form gesubmit is
if (isset($_POST["submit"])) {
	// Database connector en functies hebben we nodig
	require_once 'dbhandler-inc.php';
	require_once 'functions-inc.php';

	// Verkrijg post variabelen
	$username = $_POST["username"];
	$email = trim($_POST["email"]);
	$pwd = $_POST["password"];
	$pwdRpt = $_POST["repeat-password"];
	$agreedToTOS = $_POST["terms-of-service"];

	// Als input empty is stuur user naar signup.php?error=emptyinput
	if (emptyInputSignup($username, $email, $pwd, $pwdRpt) !== false) {
			header("location: ../signup.php?error=emptyinput");
			exit();
	}
	// Als niet akkoord is gegaan met de servicevoorwaarden stuur user naar signup.php?error=agreetoterms
	if (didntAgreeToTOS($agreedToTOS) !== false) {
		header("location: ../signup.php?error=agreetoterms");
		exit();
	}
	// Als gebruikersnaam invalide is stuur user naar signup.php?error=invalidusername
  if (invalidUsername($username) !== false) {
		header("location: ../signup.php?error=invalidusername");
		exit();
  }
	// Als email invalide is stuur user naar signup.php?error=invalidemail
  if (invalidEmail($email) !== false) {
		header("location: ../signup.php?error=invalidemail");
		exit();
  }
	// Als email of gebruikersnaam al bestaat stuur user naar signup.php?error=usernametaken
	if (usernameOrEmailExists($conn, $username, $email) !== false) {
		header("location: ../signup.php?error=usernametaken");
		exit();
  }
	// Als wachtwoord invalide is stuur user naar signup.php?error=pwdinvalid
  if (pwdInvalid($pwd) !== false) {
		header("location: ../signup.php?error=pwdinvalid");
		exit();
  }
	// Als wachtwoorden niet overeen komen stuur user naar signup.php?error=passwordsdontmatch
	if (pwdsDontMatch($pwd, $pwdRpt) !== false) {
		header("location: ../signup.php?error=passwordsdontmatch");
		exit();
	}

	// Functie uit functions-inc.php wat het user account aanmaakt
	createUser($conn, $username, $email, $pwd);
}
else {
	// Als de user de url gewoon ingetypt heeft, stuur hem terug naar signup.php
	header("location: ../signup.php");
	exit();
}
