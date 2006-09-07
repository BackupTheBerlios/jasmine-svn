<?php
/* JASmine, print accounting system for Cups.
 Copyright (C) 2005  Nayco.

 (Please read the COPYING file)

 This program is free software; you can redistribute it and/or
 modify it under the terms of the GNU General Public License
 as published by the Free Software Foundation; either version 2
 of the License, or (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program; if not, write to the Free Software
 Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA. */

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
