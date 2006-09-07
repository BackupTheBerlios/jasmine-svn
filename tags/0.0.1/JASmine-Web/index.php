<?php
  /* Index.php: Main file
       Version: 0-19.05.2005 */

  // Do some includes.
  include_once("config.php");
  include_once("libError.php");
  include_once("header.php");
  include_once("menu.php");
  
  // Include a file to fill the main body of the page, based on the $_GET[section] variable.
  // If the requested file is not found, fallback to $DEFAULT_STARTPAGE (Defined in config.php)
  if (isset($_GET[section]) && file_exists($_GET[section].".php")){
    include_once($_GET[section].".php");
  }
  else{
    include_once($DEFAULT_STARTPAGE.".php");
  }

  // Display errors here
  ER_display_errors();

  // Ending includes...
  include_once("footer.php");
?>