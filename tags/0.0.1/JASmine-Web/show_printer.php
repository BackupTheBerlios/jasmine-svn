<?php
  /* Show_printer.php: Displays stats for a given printer, 
     passed with $_GET['printer']. */
     
  // Includes
  //include_once("libJasReports.php");
  

  // Connect to the DB
  //DB_connect($DB_host,$DB_login,$DB_pass);
  //DB_select($DB_db);
  
  // Get the username
  $printer=$_GET['printer'];
  
?>
    <!-- Begin printer stats -->
    <h2>Stats for printer "<strong><?php echo $printer; ?></strong>"</h2>
    <p>
      <em>Here will appear some stats for <?php echo $printer; ?> :
      Wait until a later release ;-) !</em>
    </p>
    <!-- End printer stats -->