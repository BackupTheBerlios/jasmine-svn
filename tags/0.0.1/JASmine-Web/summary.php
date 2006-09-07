<?php
  /* Summary.php: Displays a summary of overall  printing
     activity.
       Version: 0-19.05.2005 */

  include_once("libJasReports.php");

  DB_connect($DB_host,$DB_login,$DB_pass);
  DB_select($DB_db);

  $Top10Users=jas_getUserRankings(10);
  $Top5Printers=jas_getPrinterRankings(5);

?><!-- Begin Summary -->
<h2>Summary</h2>
<h3>Users Top10</h3>
<?=$Top10Users?>
<h3>Printers Top5</h3>
<?=$Top5Printers?>
<!-- End Summary -->