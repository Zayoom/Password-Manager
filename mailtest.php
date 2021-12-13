<?php

$to      = '120007343@sgtedu.nl';
$subject = 'Het onderwerp';
$message = 'Hallo\nDoei';
$headers = '"From: Manager <manager@pasmanager.nglyceum.eu>"' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

$mailthing = mail($to, $subject, $message);

if ($mailthing === true) {
	echo "<h1>true</h1>";
}
else if ($mailthing === false) {
	echo "<h1>false</h1>";
}