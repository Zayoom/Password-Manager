<?php

// Als de login form gesubmit is
if (isset($_POST["submit"])) {
	// Database connector en functies hebben we nodig
	require_once 'dbhandler-inc.php';
	require_once 'functions-inc.php';

	// Verkrijg post variabelen
	$username = $_POST["username"];
	$pwd = $_POST["password"];

	// Als input empty is stuur user naar signin.php?error=emptyinput
	if (emptyInputLogin($username, $pwd) !== false) {
			header("location: ../signin.php?error=emptyinput");
			exit();
	}

	// Functie uit functions-inc.php wat de user inlogt
	signInUser($conn, $username, $pwd);
}
else {
	// Als de user de url gewoon ingetypt heeft, stuur hem terug naar index.php
	header("location: ../index.php");
	exit();
}
