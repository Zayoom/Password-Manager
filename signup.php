<?php
	include_once 'header.php';
?>

<main class="account-form">
	<h1>Registreer gratis!</h1>
	<?php
		// Als de url iets bevat met "pasmanager.nglyceum.eu/signup.php?error="
		if (isset($_GET["error"])) {
				// Bij url "pasmanager.nglyceum.eu/signup.php?error=emptyinput"
				if ($_GET["error"] == "emptyinput") {
					echo "<p class='error-text'>Alle velden moeten ingevuld zijn!</p>";
				}
				// Bij url "pasmanager.nglyceum.eu/signup.php?error=agreetoterms"
				else if ($_GET["error"] == "agreetoterms") {
					echo "<p class='error-text'>De servicevoorwaarden moeten eerst aanvaard zijn!</p>";
				}
				// Bij url "pasmanager.nglyceum.eu/signup.php?error=invalidusername"
				else if ($_GET["error"] == "invalidusername") {
					echo "<p class='error-text'>De gebruikersnaam mag alleen karakters, nummers en underscores (_) hebben, maar geen spaties!</p>";
				}
				// Bij url "pasmanager.nglyceum.eu/signup.php?error=invalidemail"
				else if ($_GET["error"] == "invalidemail") {
					echo "<p class='error-text'>U heeft geen valide email adres ingediend!</p>";
				}
				// Bij url "pasmanager.nglyceum.eu/signup.php?error=usernametaken"
				else if ($_GET["error"] == "usernametaken") {
					echo "<p class='error-text'>De ingediende gebruikersnaam (hoofdlettergevoelig) of e-mail is al in gebruik!</p>";
				}
				// Bij url "pasmanager.nglyceum.eu/signup.php?error=pwdinvalid"
				else if ($_GET["error"] == "pwdinvalid") {
					echo "<p class='error-text'>Uw wachtwoord moet ten minste 8 karakters en ten minste 1 nummer bevatten!</p>";
				}
				// Bij url "pasmanager.nglyceum.eu/signup.php?error=passwordsdontmatch"
				else if ($_GET["error"] == "passwordsdontmatch") {
					echo "<p class='error-text'>De wachtwoorden waren niet gelijk aan elkaar!</p>";
				}
				// Bij url "pasmanager.nglyceum.eu/signup.php?error=stmtfailed"
				else if ($_GET["error"] == "stmtfailed") {
					echo "<p class='error-text'>Er ging iets fout met de server, probeer opnieuw!</p>";
				}
				// Bij url "pasmanager.nglyceum.eu/signup.php?error=none", oftewel geen errors
				else if ($_GET["error"] == "none") {
					echo "<p>U heeft met succes een account aangemaakt! <u><a href='signin.php'>Log in</a></u> om deze te gebruiken!</p>";
				}
		}
	?>
	<!-- Voor het laten inloggen van de user moet een form aangemaakt worden -->
	<!-- Met post geven we aan dat de gebruiker iets wil sturen naar de server. -->
	<!-- Als de user op submit drukt, worden de waarden van de input elementen gestuurd naar "includes/signup-inc.php" -->
	<form action="includes/signup-inc.php" method="POST">
		<input type="e-mail" class="input-box" name="email" placeholder="E-mail">
		<input type="username" class="input-box" name="username" placeholder="gebruikersnaam">
		<input type="password" class="input-box" name="password" placeholder="Meester wachtwoord*">
		<input type="password" class="input-box" name="repeat-password" placeholder="Herhaal meester wachtwoord">
		<br />
		<br />
		<input type="checkbox" name="terms-of-service">
		<label for="terms-of-service">Ik accepteer de servicevoorwaarden</label>
		<button type="submit" class="submit-btn" name="submit">Sign up</button>
	</form>
	<p>* = Uw meester wachtwoord moet minstens acht karakters bevatten, waarvan ten minste één cijfer.</p>
	<hr>
	<p>OF</p>
	<!-- Button om de gebruiker naar de signin pagina te sturen -->
	<p>Heeft u al een account? <u><a href="signin.php">Log in</a></u></p>
</main>

<?php
	include_once 'footer.php'
?>
