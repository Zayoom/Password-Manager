<?php
	include_once 'header.php';
?>

<main class="account-form">
	<h1>Log in</h1>
	<?php
		// Als de url iets bevat met "pasmanager.nglyceum.eu/signin.php?error="
		if (isset($_GET["error"])) {
				// Bij url "pasmanager.nglyceum.eu/signin.php?error=emptyinput"
				if ($_GET["error"] == "emptyinput") {
					echo "<p class='error-text'>Vul alle velden in!</p>";
				}
				// Bij url "pasmanager.nglyceum.eu/signin.php?error=wrongusername"
				else if ($_GET["error"] == "wrongusername") {
					echo "<p class='error-text'>Uw gebruikersnaam of email is incorrect.</p>";
				}
				// Bij url "pasmanager.nglyceum.eu/signin.php?error=wrpngpassword"
				else if ($_GET["error"] == "wrongpassword") {
					echo "<p class='error-text'>Het opgegeven wachtwoord is incorrect.</p>";
				}
		}
	?>
	<!-- Voor het laten inloggen van de user moet een form aangemaakt worden -->
	<!-- Met post geven we aan dat de gebruiker iets wil sturen naar de server. -->
	<!-- Als de user op submit drukt, worden de waarden van de input elementen gestuurd naar "includes/signin-inc.php" -->
	<form action="includes/signin-inc.php" method="POST">
		<input type="text" class="input-box" name="username" placeholder="Uw Email/Gebruikersnaam">
		<input type="password" class="input-box" name="password" placeholder="Uw Meester wachtwoord">
		<button type="submit" class="submit-btn" name="submit">Log in</button>
	</form>
	<hr>
	<p>OF</p>
	<!-- Voor gebruikers die nog geen account hebben -->
	<span>
		<p>Heeft u geen account?</p>
		<u><a href="signup.php">Registreer nu!</a></u>
	</span>
</main>

<?php
	include_once 'footer.php'
?>
