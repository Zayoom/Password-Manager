<?php

////////////////////
/*Signup functies*/
///////////////////

// Checkt of alle velden wel zijn ingevuld
function emptyInputSignup($name, $email, $username, $pwd, $pwdRepeat) {
	$result;
	if (empty($name) || empty($email) || empty($username) || empty($pwd) || empty($pwdRepeat)) {
		$result = true;
	}
	else {
		$result = false;
	}
	return $result;
}

// Kijkt of de username geen tekens van a-z, A-Z of 0-9 bevat, in dat geval is de username invalide, anders niet
function invalidUsername($username) {
	$result;
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
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$result = true;
	}
	else {
		$result = false;
	}
	return $result;
}

// Kijkt of de data in het wachtwoord veld niet gelijk is aan het wachtwoord herhaal veld, dan matchen de passwords niet
function pwdsDontMatch($pwd, $pwdRepeat) {
	$result;
	if ($pwd !== $pwdRepeat) {
		$result = true;
	}
	else {
		$result = false;
	}
	return $result;
}

// Multifunctioneel, wordt gebruikt in de functie loginUser en in signup-inc.php. Checkt of de username of e-mail in de database gevonden is. Bij een ja, bestaat de username of e-mail al.
// Deze functie is extra complex, omdat we gebruik maken van prepared statements. We willen natuurlijk geen sql-injections.
function usernameOrEmailExists($conn, $username, $email) {

	// De sql die we naar de database sturen
	$sql = "SELECT * FROM users WHERE usersUsername = ? OR usersEmail = ?;";
	$stmt = mysqli_stmt_init($conn);

	if (!mysqli_stmt_prepare($stmt, $sql)) {
		// Er is iets mis met het ophalen van de data of er is geprobeerd een sql-injection te doen
		header("location: ../signup.php?error=stmtfailed");
		exit();
	}

	// Nu weten we dat de data die de user gesubmit heeft veilig is voor de database

	mysqli_stmt_bind_param($stmt, "ss", $username, $email);
	mysqli_stmt_execute($stmt);

	$resultData = mysqli_stmt_get_result($stmt);

	if ($row = mysqli_fetch_assoc($resultData)) {
		return $row;
	}
	else {
		$result = false;
		return $result;
	}

	mysqli_stmt_close($stmt);
}

// Kijkt of de password voldoet aan de eisen. Het moet een cijfer bevatten en ten minste 8 karakters lang zijn.
function pwdInvalid($pwd) {
	$result;
	if (preg_match("/[0-9]/", $pwd)
		&& strlen($pwd) >= 8) {
		$result = false;
	}
	else {
		$result = true;
	}
	return $result;
}

// Zet de user in de database d.m.v. de informatie die de user heeft opgegeven. Opnieuw prepared statements.
function createUser($conn, $name, $email, $username, $pwd) {

	// De sql die we naar de database sturen
	$sql = "INSERT INTO users (usersName, usersEmail, usersUsername, usersPwd) VALUES (?, ?, ?, ?);";
	$stmt = mysqli_stmt_init($conn);
	
	if (!mysqli_stmt_prepare($stmt, $sql)) {
	// Er is iets mis met het ophalen van de data of er is geprobeerd een sql-injection te doen
	header("location: ../signup.php?error=stmtfailed");
	exit();
	}
	
	// Voor extra security hashen we de password
	$hashedPwd = password_hash($pwd, PASSWORD_DEFAULT);
	//
	mysqli_stmt_bind_param($stmt, "ssss", $name, $email, $username, $hashedPwd);
	mysqli_stmt_execute($stmt);
	
	mysqli_stmt_close($stmt);

	header("location: ../signup.php?error=none");
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
function loginUser($conn, $username, $pwd) {
	$usernameOrEmailExists = usernameOrEmailExists($conn, $username, $username);

	// Check of de username of e-mail die de user gesubmit heeft in de database staat
	if ($usernameOrEmailExists === false) {
		header("location: ../login.php?error=wronglogin");
		exit();
	}

	// Aangezien de password in de database gehashed was, moeten we hem verifiëren
	$pwdHashed = $usernameOrEmailExists["usersPwd"];
	$checkedPwd = password_verify($pwd, $pwdHashed);

	// Check of de pawword die de user gesubmit heeft in de database staat
	if ($checkedPwd === false) {
		header("location: ../login.php?error=wronglogin");
		exit();
	}
	else if ($checkedPwd === true) {
		// De user heeft de juiste gegevens ingevuld
		session_start();
		// Start een sessie met sessie variabele userusername. Die gaan we gebruiken om te checken of de user is ingelogd en voor een leuke welkom tekst in index.php
		$_SESSION["userusername"] = $usernameOrEmailExists["usersUsername"];
		header("location: ../index.php");
		exit();
	}
}

////////////////////
/*Product functies*/
////////////////////

//Laadt producten uit de database en zet ze als array in variabele $allData
function loadProducts($conn) {

	// Selecteer de gegevens die we nodig hebben uit de database
	$sql = "SELECT productName, productActualPrice, productImage, productDescription FROM products;";
	$result = $conn->query($sql);

	// Initialiseer $allData als array, anders denkt array_push() dat het geen array is
	$allData = array();

	// Terwijl we alle data ophalen, zet deze data een voor een in array $allData
	while($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
		array_push($allData, $row);
	}

	$conn->close();
	return $allData;
}

//////////////////////////
/*Shoppingcart functies*/
/////////////////////////

/* Belangrijk:
	$_SESSION['productsincart'] is een array met alle producten die in je winkelmandje hebt gedaan. Waar de variabele voor staat:
	[product1[productNaam, productPrijs, etc]
	product2[productNaam, productPrijs, etc]
	product3[productNaam, productPrijs, etc]]

	$_SESSION['productsincart'] bevat dus een array met producten, en elk product is een array met informatie over dat product
*/

// Slaat productinformatie op in session variabele productsincart, zodat de hele sessie de info van je winkelmandje blijft staan in shoppingcart.php
function storeProductByName($conn, $productName, $productQuantity) {

	// Selecteer het product waarvan productName = de naam van het product die we willen opslaan, namelijk $productName
	$sql = "SELECT productName, productActualPrice, productImage FROM products WHERE productName = '" . $productName . "';";
	$result = $conn->query($sql);

	// Als session variabele productsincart nog niet bestaat, moeten we het initializeren aangeven dat het een array moet zijn, anders werkt array_push() niet
	if (!isset($_SESSION['productsincart'])) {
		$_SESSION['productsincart'] = array();
	}

	// Als session variabele productsincart al bestaat
	else {

		// Loop door session variabele productsincart
		foreach ($_SESSION['productsincart'] as $key => $value) {

			// Als we de productnaam $productName tegenkomen in $_SESSION['productsincart'] willen we natuurlijk niet twee keer hetzelfde product in ons winkelmandje
			if ($value[0] == $productName) {

				// Dus tel de hoeveelheid van het oude product op bij de hoeveelheid van het niewe, overbodige product
				$_SESSION['productsincart'][$key][3] = $value[3] + $productQuantity;
				return;
			}
		}
	}

	// Terwijl we alle data ophalen, zet deze data in array $_SESSION['productsincart']
	while($row = mysqli_fetch_assoc($result)) {
		array_push($_SESSION['productsincart'], array($row['productName'], $row['productActualPrice'], $row['productImage'], $productQuantity));
	}
	$conn->close();
}

// Verwijdert product uit winkelmandje wanneer je op het kruisje drukt
function deleteProductByName($conn, $productName) {

	// session_start() omdat anders $_SESSION['productsincart'] niet herkend wordt
	session_start();

	// Loop door $_SESSION['productsincart']
	foreach ($_SESSION['productsincart'] as $key => $value) {

		// Als de waarde van $productName in $_SESSION['productsincart'] staat
		if ($value[0] == $productName) {

			// Zet dit product uit array $_SESSION['productsincart']
			unset($_SESSION['productsincart'][$key]);
			// Zorg ervoor dat het recent verwijderde product wordt opgevuld door het product wat ernaast staat (sorteert de array opnieuw)
			array_values($_SESSION['productsincart']);

			header("location: ../shoppingcart.php");
			exit();
		}
	}
}

///////////////////
/*Review functies*/
///////////////////

// Check of het tekstveld leeg is
function emptyInputReview($reviewText, $reviewRating) {
	$result = false;

	if (empty($reviewText) || empty($reviewRating)) {
		$result = true;
	}
	return $result;
}

// Stelt de rating die de user geeft bij
function adjustRating($reviewRating) {

	// Als de user een negatief getal als rating geeft wordt rating 0
	if ($reviewRating < 0) {
		return 0;
	}

	// Als de user hoger dan 10 als rating geeft wordt rating 10
	else if ($reviewRating > 10) {
		return 10;
	}

	// In alle andere gevallen is de rating prima
	else {
		return $reviewRating;
	}
}

// Zet de review in de database waarbij we uitkijken voor sql-injection door middel van prepared statements
function createReview($conn, $reviewText, $reviewSenderName, $reviewRating) {

	// De sql die we naar de database sturen
	$sql = "INSERT INTO reviews (reviewText, reviewSenderName, reviewRating) VALUES (?, ?, ?);";
	$stmt = mysqli_stmt_init($conn);
		if (!mysqli_stmt_prepare($stmt, $sql)) {
		// Er is iets mis met het ophalen van de data of er is geprobeerd een sql-injection te doen
		header("location: ../reviews.php?error=stmtfailed");
		exit();
	}

	// Nu weten we dat de data die de user gesubmit heeft veilig is voor de database

	mysqli_stmt_bind_param($stmt, "ssi", $reviewText, $reviewSenderName, $reviewRating);
	mysqli_stmt_execute($stmt);

	mysqli_stmt_close($stmt);

	header("location: ../reviews.php?error=none");
}

// Laadt reviews uit de database en zet ze in reviews.php
function loadReviews($conn) {

	// Selecteer de gegevens die we nodig hebben uit de database
	$sql = "SELECT reviewId, reviewText, reviewSenderName, reviewRating FROM reviews;";
	$result = $conn->query($sql);

	// Initialiseer $allData als array, anders denkt array_push() dat het geen array is
	$allData = [];

	// Terwijl we alle data ophalen, zet deze data een voor een in array $allData
	while($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
		array_push($allData, $row);
	}
	$conn->close();
	return $allData;
}

// Verwijdert de review met die specifieke id
function deleteReviewById($conn, $reviewId) {

	// De sql die we naar de database sturen
	$sql = "DELETE FROM reviews WHERE reviewId = " . $reviewId . ";";
	$result = $conn->query($sql);

	$conn->close();
	header("location: ../reviews.php");
}
