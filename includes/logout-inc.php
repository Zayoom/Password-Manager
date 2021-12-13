<?php
  // Dit script logt de user uit door de sessie variabele te vernietigen

  // session_start() moet gecalld worden, want deze file weet niet dat er al een sessie gestart is
  session_start();

  // Vernietig de sessie
  session_unset();
  session_destroy();

  // Stuur de user terug naar index.php
  header("location: ../index.php");
  exit();
