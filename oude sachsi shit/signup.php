<?php
  include_once 'header.php';
?>

<section data-role='content' data-theme='a'>
  <center><h1>Sign up</h1></center>
</section>

<section class='centeredtxt' data-role='content' data-theme='a'>
  <form data-ajax='false' action="includes/signup-inc.php"  method="POST">
    <input class='centeredtxt fontmedium' data-role='content' data-theme='b' type="text" name="fullname" placeholder="Full Name">
    <input class='centeredtxt fontmedium' data-role='content' data-theme='b' type="text" name="email" placeholder="E-mail Adress">
    <input class='centeredtxt fontmedium' data-role='content' data-theme='b' type="text" name="username" placeholder="Username (no symbols or spaces)">
    <p class='fontsmall'>(Passwords need at least 8 characters and one number)</p>
    <input class='centeredtxt fontmedium' data-role='content' data-theme='b' type="password" name="pwd" placeholder="Password">
    <input class='centeredtxt fontmedium' data-role='content' data-theme='b' type="password" name="pwdrepeat" placeholder="Repeat password">
    <button class='fontmedium' data-role='button' data-theme='c' type="submit" name="submit">Create Account</button>
  </form>
  <?php
    if (isset($_GET["error"])) {
			// Als er een error in de url staat
  			if ($_GET["error"] == "emptyinput") {
  				echo "<p>Please fill in all fields!</p>";
  			}
  			else if ($_GET["error"] == "invalidusername") {
  				echo "<p>Usernames can only have characters and numbers, but no spaces!</p>";
  			}
  			else if ($_GET["error"] == "invalidemail") {
  				echo "<p>You did not submit a valid email adress!</p>";
  			}
  			else if ($_GET["error"] == "pwdinvalid") {
  				echo "<p>Your password needs at least eight characters, with at least one number!</p>";
  			}
  			else if ($_GET["error"] == "passwordsdontmatch") {
  				echo "<p>The passwords don't match!</p>";
  			}
  			else if ($_GET["error"] == "usernametaken") {
  				echo "<p>The submitted username (case sensitive) or e-mail is already taken!</p>";
  			}
  			else if ($_GET["error"] == "stmtfailed") {
  				echo "<p>Something went wrong, try again!</p>";
  			}
  			else if ($_GET["error"] == "none") {
  				echo "<p>You have signed up successfully! Log in to access your account!</p>";
  			}
    }
  ?>
</section>

<?php
  include_once 'footer.php';
?>
