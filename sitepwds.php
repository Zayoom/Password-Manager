<?php
	include_once 'header.php';

  if (!isset($_SESSION["userusername"])) {
    // Als de user niet ingelogd is
    header("location: ../index.php");
  }
?>

<main>
  <h2>Voeg een site toe</h2>
	<?php
		// Als de url iets bevat met "pasmanager.nglyceum.eu/signin.php?error="
		if (isset($_GET["error"])) {
				// Bij url "pasmanager.nglyceum.eu/signin.php?error=emptyinput"
				if ($_GET["error"] == "emptyinput") {
					echo "<p class='error-text>Vul alstublieft alle velden in!</p>";
				}
		}
	?>

	<!-- Dit is de form voor het invullen van informatie over de site die je wilt toevoegen -->
  <form action="includes/addsite-inc.php" method="post">
    <input type="text" name="sitename" placeholder="Site naam...*">
    <input type="text" name="siteemail" placeholder="Site email...">
    <input type="text" name="siteusername" placeholder="Site account naam...">
    <input type="password" name="sitepassword" placeholder="Site wachtwoord...*">
		<!-- De session variabele userusername wordt ook gestuurd naar includes/addsite-inc.php.
		Dit is omdat addsite-inc.php niet direct bij deze variabele kan -->
		<input type="hidden" name="username" value="<?php echo $_SESSION["userusername"]; ?>">
    <p>* = verplicht veld</p>
    <button type="submit" name="sitesubmit">Voeg site toe</button>
  </form>

	<hr>

  <section>
	<?php
		// We vertellen dat we de database handler (dbhandler-inc.php) nodig hebben en functions-inc.php voor onze functies
		include_once 'includes/dbhandler-inc.php';
		include_once 'includes/functions-inc.php';

		// Sites worden geladen door middel van de loadSites functie in functions-inc.php
		$data = loadSites($conn, $_SESSION["userusername"]);

		// Als er data is
		if ($data !== false) {

			// Doorloop de data in een for loop MAAR tel van het laatste element van de array tot het eerste element van de array.
			// Zo komen de meest recente reviews boven te staan.
			for ($i = count($data) - 1; $i >= 0; $i--) {
				// Print de site naam, account gebruikersnaam, email en site wachtwoord.
				// Hieronder is ook een form te zien. Dit is om je site info te verwijderen.
				// De $data[$i][0] geeft de id van de site informatie weer, waardoor we in de database weten welk stuk informatie verwijderd moet worden.
				echo "
				<div class='site-entry'>
					<p>Site naam: " . $data[$i][2] . "</p>
					<p>Site accountnaam: " . $data[$i][3] . "</p>
					<p>Site email: " . $data[$i][4] . "</p>
					<p><b>Site wachtwoord: " . $data[$i][5] . "</b></p>
				</div>
				<form action='includes/addsite-inc.php' method='post'>
					<input type='hidden' name='entryId' value='" . $data[$i][0] . "' />
					<button type='submit' name='deleteEntry'>Verwijder site</button>
				</form>
				<hr>";
			}
		}
	?>
  </section>
</main>

<?php
	include_once 'footer.php';
?>
