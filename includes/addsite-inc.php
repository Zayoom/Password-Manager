<?php

// Als de form gesubmit is
if (isset($_POST["sitesubmit"])) {
	// Database connector en functies hebben we nodig
	require_once 'dbhandler-inc.php';
	require_once 'functions-inc.php';

	// Verkrijg post variabelen
	$username = $_POST["username"];
	$sitename = $_POST["sitename"];
	$siteemail = $_POST["siteemail"];
	$siteusername = $_POST["siteusername"];
	$sitepwd = $_POST["sitepassword"];

	// Als input empty is stuur user naar 'sitepwds.php?error=emptyinput'
	if (emptyInputLogin($sitename, $sitepwd) !== false) {
			header("location: ../sitepwds.php?error=emptyinput");
			exit();
	}

	// Functie in functions-inc.php die zorgt voor site submit
	submitSiteDetails($conn, $username, $sitename, $siteemail, $siteusername, $sitepwd);
}
// Als op de verwijder knop is gedrukt
else if (isset($_POST["deleteEntry"])) {
	// Database connector en functies hebben we nodig
	require_once 'dbhandler-inc.php';
	require_once 'functions-inc.php';
	
	// Verkrijg post variabele
	$entryId = $_POST["entryId"];
	
	// Functie in functions-inc.php die zorgt voor het verwijderen van een site
	deleteEntryById($conn, $entryId);
}
else {
	// Als de user de url gewoon ingetypt heeft, stuur hem terug naar index.php
	header("location: ../index.php");
	exit();
}
