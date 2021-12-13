<!-- Deze pagina wordt toegevoegd aan alle pagina's, zodat alle pagina's een header hebben en deze niet telkens opnieuw gecodeerd te hoeven worden per pagina -->
<?php
	//Zorgt ervoor dat de gebruiker ingelogd blijft tijdens het navigeren op onze website
	session_start();
?>

<!-- Deze pagina bestaat uit een top bar met daarin links naar andere pagina's in onze website -->
<!DOCTYPE html>
<html>
<head>
  <title>Passknight | Veilige wachtwoordopslag</title>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- De link naar de css stylesheet -->
  <link rel="stylesheet" type="text/css" href="/css/style.css" />
</head>
<body>
  <header>
		<!-- De navigatie voor onze site met links naar andere pagina's -->
	   <nav>
       <a href="index.php">Home</a>
       <a href="how-it-works.php">Hoe werkt PassKnight?</a>
			 <?php
			 // Als de user ingelogd is zal de variabele $_SESSION["userusername"] bestaan. Dit wordt gecheckt met de isset() functie.
		    if (isset($_SESSION["userusername"])) { ?>
					<!-- Er worden extra links geladen die alleen beschikbaar zijn voor gebruikers die ingelogd zijn. -->
					<a href='sitepwds.php'>Opgeslagen sites</a>
					<a href='includes/logout-inc.php'>Log uit</a>
				<?php }
				else { ?>
					<a href='signup.php'>Registreren</a>
	        <a href='signin.php'>Inloggen</a>
				<?php }
			?>
     </nav>
  </header>
	<body>
		<?php
			if (isset($_SESSION["userusername"])) {
				// Als de user ingelogd is
				echo "<h1>Welkom " . $_SESSION["userusername"] . "!</h1>";
			}
		?>

<!-- De pagina wordt niet afgesloten met /body en /html tags, aangezien het geinclude wordt in andere pagina's -->
