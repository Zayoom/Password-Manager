<?php

// Als de login form gesubmit is
if (isset($_POST["submit"])) {
	
	// Verkrijg post variabelen
	$username = $_POST["username"];
	$email = $_POST["e-mail"];
	$pwd = $_POST["password"];
	$pwdRpt = $_POST["repeat-password"];

	require_once 'functions-inc.php';

	// Als input empty is stuur user naar dit adres
	if (emptyInputLogin($username, $pwd) !== false) {
			header("location: ../index.php?error=emptyinput");
			exit();
	}
	
	if (doPwdsMatch($pwd, $pwdRpt) !== false) {
			header("location: ../index.php?error=passwordsdontmatch");
			exit();
	}
	
	//$to = "120007343@sgtedu.nl";
	//$subject = "Test mail";
	//$message = "Hello! This is a simple email message.";
	//$from = "manager@pasmanager.nglyceum.eu";
	//$headers = "From: $from";
	//mail($to,$subject,$message,$headers);
	//echo "Mail Sent.";
}
else {
	// Als de user de url gewoon ingetypt heeft, stuur hem terug naar index.php
	header("location: ../index.php");
	exit();
}
