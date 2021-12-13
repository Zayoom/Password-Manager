<?php

////////////////////
/*Signup functies*/
///////////////////

// Checkt of alle velden wel zijn ingevuld
function emptyInputSignup($username, $email, $pwd, $pwdRpt) {
	$result;
	// De empty functie geeft alleen true als de variabele tussen haakjes geen tekst in zich heeft
	if (empty($username) || empty($email) || empty($pwd) || empty($pwdRpt)) {
		$result = true;
	}
	else {
		$result = false;
	}
	return $result;
}

// Checkt of de checkbox van terms of service aangeklikt was
function didntAgreeToTOS($agreedToTOS) {
	$result;
	// De isset functie geeft alleen true als de variabele bestaat
	if (isset($agreedToTOS)) {
		$result = false;
	}
	else {
		$result = true;
	}
	return $result;
}

// Kijkt of de username andere tekens dan a-z, A-Z of 0-9 of _ bevat, in dat geval is de username invalide, anders niet
function invalidUsername($username) {
	$result;
	// Functie preg_match geeft true als de gegeven variabele alleen karakters bevat die hieronder tussen aanhalingstekens staan
	if (!preg_match("/^[a-zA-Z0-9_]*$/", $username)) {
		$result = true;
	}
	else {
		$result = false;
	}
	return $result;
}

// Checkt of de data die in het e-mail veld is ingevuld ook echt een e-mail kan zijn
function invalidEmail($email) {
	$result;
	// Functie filter_var geeft true als in dit geval de variabele een valide e-mailadres is
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$result = true;
	}
	else {
		$result = false;
	}
	return $result;
}

// Kijkt of de password voldoet aan de eisen. Het moet een cijfer bevatten en ten minste 8 karakters lang zijn.
function pwdInvalid($pwd) {
	$result;
	// Functie preg_match geeft true als de gegeven variabele alleen karakters bevat die hieronder tussen aanhalingstekens staan
	// strlen geeft het aantal karakters aan van de variabele
	if (preg_match("/[0-9]/", $pwd)
		&& strlen($pwd) >= 8) {
		$result = false;
	}
	else {
		$result = true;
	}
	return $result;
}

// Kijkt of de data in het wachtwoord veld niet gelijk is aan het wachtwoord herhaal veld
function pwdsDontMatch($pwd, $pwdRpt) {
	$result;
	// Geeft true als de variabele van het wachtwoord niet gelijk is aan de variabele van de wachtwoordherhaling
	if ($pwd !== $pwdRpt) {
		$result = true;
	}
	else {
		$result = false;
	}
	return $result;
}

// Multifunctioneel, wordt gebruikt in de functie loginUser en in signup-inc.php. Checkt of de username OF e-mail in de database gevonden is. Bij true bestaat de username of e-mail al.
// Deze functie is bestendig tegen sql injections.
function usernameOrEmailExists($conn, $username, $email) {

	// De sql die we naar de database sturen
	$sql = "SELECT * FROM users WHERE userName = ? OR userEmail = ?;";

	// Initialiseer de connectie
	$stmt = mysqli_stmt_init($conn);

	// Bereid het statement voor en kijk na of het een valide sql statement is
	if (!mysqli_stmt_prepare($stmt, $sql)) {
		// Er is iets mis met het ophalen van de data of er is geprobeerd een sql-injection te doen
		header("location: ../signup.php?error=stmtfailed");
		exit();
	}

	// Nu weten we dat de data die de user gesubmit heeft veilig is voor de database

	// Bind de variabelen aan de parameters in het sql statement en voer het uit
	mysqli_stmt_bind_param($stmt, "ss", $username, $email);
	mysqli_stmt_execute($stmt);

	// Deze variabele bevat het resultaat van de sql statement
	$resultData = mysqli_stmt_get_result($stmt);


	if ($row = mysqli_fetch_assoc($resultData)) {
		return $row;
	}
	else {
		$result = false;
		return $result;
	}

	// Sluit de connectie met de database voor nu
	mysqli_stmt_close($stmt);
}

// Zet de user in de database op basis van de informatie die de user heeft opgegeven. Opnieuw door middel van prepared statements.
function createUser($conn, $username, $email, $pwd) {

	// De sql die we naar de database sturen
	$sql = "INSERT INTO users (userName, userEmail, userMPW) VALUES (?, ?, ?);";

	// Initialiseer de connectie
	$stmt = mysqli_stmt_init($conn);

	// Bereid het statement voor en kijk na of het een valide sql statement is
	if (!mysqli_stmt_prepare($stmt, $sql)) {
		// Er is iets mis met het ophalen van de data of er is geprobeerd een sql-injection te doen
		header("location: ../signup.php?error=stmtfailed");
		exit();
	}

	// Voor extra security hashen we de password. Dit betekent dat het wachtwoord niet ontcijferd kan worden op basis van de hash.
	$hashedPwd = password_hash($pwd, PASSWORD_DEFAULT);

	// Bind de variabelen aan de parameters in het sql statement en voer het uit
	mysqli_stmt_bind_param($stmt, "sss", $username, $email, $hashedPwd);
	mysqli_stmt_execute($stmt);

	// Sluit de connectie met de database voor nu
	mysqli_stmt_close($stmt);

	// Stuur de gebruiker naar '/signup.php?error=none', alles is goed gegaan
	header("location: ../signup.php?error=none");
	exit();
}

//////////////////
/*Login functies*/
/////////////////

// Checkt of alle velden wel zijn ingevuld
function emptyInputLogin($username, $pwd) {
	$result;
	if (empty($username) || empty($pwd)) {
		$result = true;
	}
	else {
		$result = false;
	}
	return $result;
}

// Logt de user in
function signInUser($conn, $username, $pwd) {
	// Gebruik de usernameOrEmailExists functie om te kijken of de username of email wel bestaat.
	$usernameOrEmailExists = usernameOrEmailExists($conn, $username, $username);

	// Check of de username of e-mail die de user gesubmit heeft in de database staat
	if ($usernameOrEmailExists === false) {
		// Verkeerde login.
		header("location: ../signin.php?error=wrongusername");
		exit();
	}

	// Aangezien de password in de database gehashed was, gaan we ze vergelijken door middel van de password_verify functie
	$pwdHashed = $usernameOrEmailExists["userMPW"];
	$checkedPwd = password_verify($pwd, $pwdHashed);

	// Check of de pawword die de user gesubmit heeft in de database staat
	if ($checkedPwd === false) {
		// Verkeerd wachtwoord
		header("location: ../signin.php?error=wrongpassword");
		exit();
	}
	// Als het wachtwoord klopt
	else if ($checkedPwd === true) {
		// De user heeft de juiste gegevens ingevuld
		// Start een sessie met sessie variabele userusername. Die gaan we gebruiken om te checken of de user is ingelogd en voor een leuke welkom tekst in header.php
		session_start();
		$_SESSION["userusername"] = $usernameOrEmailExists["userName"];

		// Ga naar 'sitepwds.php'
		header("location: ../sitepwds.php");
		exit();
	}
}

/////////////////////
/*Addsite functies*/
////////////////////

// Verkrijgt master password voor de gespecifieerde username
function getMPW($conn, $username) {
	// Het sql statement
	$sql = "SELECT userMPW FROM users WHERE userName = ?;";
	// Initialiseer de connectie
	$stmt = mysqli_stmt_init($conn);

	// Bereid het statement voor en kijk na of het een valide sql statement is
	if (!mysqli_stmt_prepare($stmt, $sql)) {
		// Er is iets mis met het ophalen van de data of er is geprobeerd een sql-injection te doen
		header("location: ../signup.php?error=stmtfailed");
		exit();
	}

	// Nu weten we dat de data die de user gesubmit heeft veilig is voor de database

	// Bind de variabelen aan de parameters in het sql statement en voer het uit
	mysqli_stmt_bind_param($stmt, "s", $username);
	mysqli_stmt_execute($stmt);

	// Bevat het resultaat van het sql statement
	$resultData = mysqli_stmt_get_result($stmt);

	if ($row = mysqli_fetch_assoc($resultData)) {
		return $row;
	}
	else {
		$result = false;
		return $result;
	}

	// Sluit de connectie met de database voor nu
	mysqli_stmt_close($stmt);
}

// Zorgt voor het submitten van site info
function submitSiteDetails($conn, $username, $sitename, $siteemail, $siteusername, $pwd) {
	// Verkrijg master password van de gebruiker
	$queried_mpw = getMPW($conn, $username);

	// Eerst versleutelen we het wachtwoord wat de user heeft opgegeven door middel van AES 128 bit
	$encrypted_string = openssl_encrypt($pwd,"AES-128-ECB",$queried_mpw["userMPW"]);

	// Als er geen master password is
	if (!$queried_mpw) {
		// Er is iets mis met het ophalen van de data
		header("location: ../sitepwds.php?error=stmtfailed");
		exit();
	}

	// De sql die we naar de database sturen
	$sql = "INSERT INTO site_passwords (userName, siteName, siteUsername, siteEmail, sitePwd) VALUES (?, ?, ?, ?, ?);";
	// Initialiseer de connectie
	$stmt = mysqli_stmt_init($conn);

	// Bereid het statement voor en kijk na of het een valide sql statement is
	if (!mysqli_stmt_prepare($stmt, $sql)) {
		// Er is iets mis met het ophalen van de data of er is geprobeerd een sql-injection te doen
		header("location: ../sitepwds.php?error=stmtfailed");
		exit();
	}

	// Nu weten we dat de data die de user gesubmit heeft veilig is voor de database

	// Bind de variabelen aan de parameters in het sql statement en voer het uit
	mysqli_stmt_bind_param($stmt, "sssss", $username, $sitename, $siteusername, $siteemail, $encrypted_string);
	mysqli_stmt_execute($stmt);

	// Sluit de connectie voor nu
	mysqli_stmt_close($stmt);

	header("location: ../sitepwds.php?error=none");
	exit();
}

// Functie die sites van de user laadt als deze op sitepwds.php kijkt.
// We hoeven nu geen prepared statements te gebruiken, aangezien er alleen maar wat opgevraagd wordt uit de database
// en er dus geen sql injection plaats kan vinden.
function loadSites($conn, $username) {
	// Verkrijg master password van de gebruiker
	$queried_mpw = getMPW($conn, $username);

	// Selecteer de gegevens die we nodig hebben uit de database
	$sql = "SELECT * FROM site_passwords WHERE userName = '" . $username . "';";
	$result = $conn->query($sql);

	// Initialiseer $allData als array, anders denkt array_push() dat het geen array is
	$allData = [];

	// Terwijl we alle data ophalen, zet deze data een voor een in array $allData
	while($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
		array_push($allData, $row);
	}
	// Sluit de connectie voor nu
	$conn->close();

	// De wachtwoorden zijn nog niet ontsleuteld. Daar zorgt deze foreach loop voor.
	// Er wordt gekeken naar elk item wat geselecteerd is door de sql statement
	foreach ($allData as $key => $value) {
		// openssl_decrypt ontcijfert het wachtwoord.
		// Voor elk item in de database zit deze op plaats index 5. Vandaar $allData[$key][5].
		$decrypted_string = openssl_decrypt($allData[$key][5], "AES-128-ECB", $queried_mpw["userMPW"]);
		$allData[$key][5] = $decrypted_string;
	}
	return $allData;
}

// Verwijdert de site met die specifieke id.
// We hoeven nu geen prepared statements te gebruiken, aangezien er alleen maar wat opgevraagd wordt uit de database
// en er dus geen sql injection plaats kan vinden.
function deleteEntryById($conn, $entryId) {

	// De sql die we naar de database sturen
	$sql = "DELETE FROM site_passwords WHERE pwdId = " . $entryId . ";";
	$result = $conn->query($sql);

	// Sluit de connectie voor nu
	$conn->close();

	// Stuur gebruiker naar sitepwds.php
	header("location: ../sitepwds.php");
}
