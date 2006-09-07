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

/* libJasReports.php: Stores functions related to JASmine reports,
   such as printer stats, user stats, etc....

   Some global variables can be set in the calling scripts to modify the
   behaviour of some of the following functions :
   $jas_userStatsPage : Sets the url to use to display user stats; see below.
   $jas_printerStatsPage : Sets the url to use to display printer stats. If
     this variable is set, functions like "jas_getUserLastJobs()" will display
     the printer fields as an hyperlink. The link's url will be made from the
     content of the $jas_printerStatsPage variable, followed by the printer
     name. Example: If the printer is "printer_1" and $jas_printerStatsPage
     is set to
       "viewPrinter.php?printer="
     the resulting url will be
       <a href="viewPrinter.php?printer=printer_1">printer_1</a>
     The viewPrinter.php must be coded accordingly to display printer stats
     for the given printer... Same goes for $jas_userStatsPage, but instead
     it deal with user stats. For working example, well, take a look at
     JASmine-Web's config file ;-)

     Version 0-7.11.2005 */

   include_once("libDB_mysql.php");
   include_once("libError.php");
   include_once("libTables.php");

   /* jas_getUserTotalPages: Returns the total number of pages for
      a given user, as an integer, for all jobs on all printers. */
  function jas_getUserTotalPages($userName){
    if (!$userName){
      $source="jas_getUserTotalPages";
      $message="Missing userName !";
      $hint="Please specify \$userName for this function.";
      $severity=ERWARN;
      ER_log_error($source, $message, $severity, $hint);
      return false;
    }
    // Clean the input variable
    $userName=DB_escape_string($userName);
    $query="SELECT SUM(copies*pages) as total FROM jobs_log WHERE user=$userName";
    if ($result=DB_query($query)){ //Assignment !
      $row=mysql_fetch_row($result);
      mysql_free_result($result);
      if (!empty($row[0]))
        return $row[0];
      else
        return 0;
    }
    else{
      $source="jas_getUserTotalPages";
      $message="Query failed !";
      $hint="Check for the query syntax, and that the MySQL host is up.";
      $severity=ERWARN;
      ER_log_error($source, $message, $severity, $hint);
      return false;
    }
  }

  /* jas_getUserTotalPagesByPrinter: Returns the total number of pages
     for a given user, as an integer, for all jobs, on a given printer */
  function jas_getUserTotalPagesByPrinter($userName, $printerName){
    if (!$userName || !$printerName){
      $source="jas_getUserTotalPagesByPrinter";
      $message="Missing userName and/or printerName !";
      $hint="Please specify \$userName and/or \$printerName for this function.";
      $severity=ERWARN;
      ER_log_error($source, $message, $severity, $hint);
      return false;
    }
    // Clean the input variables
    $userName=DB_escape_string($userName);
    $printerName=DB_escape_string($printerName);
    $query="SELECT SUM(copies*pages) as total FROM jobs_log WHERE user=$userName AND printer=$printerName";
    if($result=DB_query($query)){ //Assignment !
      $row=mysql_fetch_row($result);
      mysql_free_result($result);
      if (!empty($row[0]))
        return $row[0];
      else
        return 0;
    }
    else{
      $source="jas_getUserTotalPagesByPrinter";
      $message="Query failed !";
      $hint="Check for the query syntax, and that the MySQL host is up.";
      $severity=ERWARN;
      ER_log_error($source, $message, $severity, $hint);
      return false;
    }
  }

   /* jas_getPrinterTotalPages: Returns the total number of pages for
      a given printer, as an integer, for all jobs from all user. */
  function jas_getPrinterTotalPages($printerName){
    if (!$printerName){
      $source="jas_getPrinterTotalPages";
      $message="Missing printerName !";
      $hint="Please specify \$printerName for this function.";
      $severity=ERWARN;
      ER_log_error($source, $message, $severity, $hint);
      return false;
    }
    // Clean the input variable
    $printerName=DB_escape_string($printerName);
    $query="SELECT SUM(copies*pages) as total FROM jobs_log WHERE printer=$printerName";
    if ($result=DB_query($query)){ //Assignment !
      $row=mysql_fetch_row($result);
      mysql_free_result($result);
      if (!empty($row[0]))
        return $row[0];
      else
        return 0;
    }
    else{
      $source="jas_getPrinterTotalPages";
      $message="Query failed !";
      $hint="Check for the query syntax, and that the MySQL host is up.";
      $severity=ERWARN;
      ER_log_error($source, $message, $severity, $hint);
      return false;
    }
  }


  /* jas_getUserLastJobs: Returns an array containing the history of
     the last print jobs that a given user printed since $days days ago.
     Optionnaly, a printer name can be given, restricting the count to
     only this printer. */
  function jas_getUserLastJobs($userName, $days, $printerName=""){
    if (!$userName || !$days){
      $source="jas_getUserLastJobs";
      $message="Missing userName and/or number of days !";
      $hint="Please specify \$userName and/or \$days for this function.";
      $severity=ERWARN;
      ER_log_error($source, $message, $severity, $hint);
      return false;
    }
    // Clean the input variables and prepare query
    $userName=DB_escape_string($userName);
    $days=DB_escape_string($days);

    $query="SELECT date,title,server, printer,(copies*pages) as total FROM jobs_log ";
    $query.="WHERE DATE_SUB(CURDATE(),INTERVAL $days DAY) <= date AND user=$userName ";

    if (!empty($printerName)){
      $printerName=DB_escape_string($printerName);
      $query.="AND printer=$printerName ";
    }

    $query.="ORDER BY DATE DESC";

    if($result=DB_query($query)){ //Assignment !
//      return DB_Dump_Result($result);

      $tableULJ=new TBL_table();
      $tableULJ->setCaption("Last jobs for user $userName");
      $tableULJ->setColumns(array('date', 'title', 'server', 'printer', 'total'));

      global $jas_printerStatsPage;

      while ($row=mysql_fetch_assoc($result)){
        if (isset($jas_printerStatsPage))
          $row['printer']="<a href=\"".$jas_printerStatsPage.$row['printer']."\">".$row['printer']."</a>";
        $tableULJ->addRow($row);
      }

      mysql_free_result($result);
      return $tableULJ->displayTable('20');
    }
    else{
      $source="jas_getUserLastJobs";
      $message="Query failed !";
      $hint="Check for the query syntax, and that the MySQL host is up.";
      $severity=ERWARN;
      ER_log_error($source, $message, $severity, $hint);
      return false;
    }
  }

  /* jas_getPrinterLastJobs: Returns an array containing the history of
     the last print jobs that a given printer printed since $days days ago. */
  function jas_getPrinterLastJobs($printerName, $days){
    if (!$printerName || !$days){
      $source="jas_getPrinterLastJobs";
      $message="Missing printerName and/or number of days !";
      $hint="Please specify \$printerName and/or \$days for this function.";
      $severity=ERWARN;
      ER_log_error($source, $message, $severity, $hint);
      return false;
    }
    // Clean the input variables and prepare query
    $printerName=DB_escape_string($printerName);
    $days=DB_escape_string($days);

    $query="SELECT date,title,server, user,(copies*pages) as total FROM jobs_log ";
    $query.="WHERE DATE_SUB(CURDATE(),INTERVAL $days DAY) <= date AND printer=$printerName ";
    $query.="ORDER BY DATE DESC";

    if($result=DB_query($query)){ //Assignment !
//      return DB_Dump_Result($result);

      $tableULJ=new TBL_table();
      $tableULJ->setCaption("Last jobs for $printerName");
      $tableULJ->setColumns(array('date', 'title', 'server', 'user', 'total'));

      global $jas_userStatsPage;

      while ($row=mysql_fetch_assoc($result)){
        if (isset($jas_userStatsPage))
          $row['user']="<a href=\"".$jas_userStatsPage.$row['user']."\">".$row['user']."</a>";
        $tableULJ->addRow($row);
      }

      mysql_free_result($result);
      return $tableULJ->displayTable('20');
    }
    else{
      $source="jas_getPrinterLastJobs";
      $message="Query failed !";
      $hint="Check for the query syntax, and that the MySQL host is up.";
      $severity=ERWARN;
      ER_log_error($source, $message, $severity, $hint);
      return false;
    }
  }


  /* jas_getUserRankings: Returns an associative array containing
     the $count most paper consuming users. Optionnaly, the results
     can be limited to either those who have printed more than
     $threshold pages, on $printerName OR from $dpt (These last
     two need some thinking before implementation) */
  function jas_getUserRankings($count, $threshold=0, $printerName="", $dpt=""){
     if (!$count){
      $source="jas_getUserRankings";
      $message="Missing number of results to return !";
      $hint="Please specify \$count for this function.";
      $severity=ERWARN;
      ER_log_error($source, $message, $severity, $hint);
      return false;
    }
    // Clean the input variables and prepare query
    $count=DB_escape_string($count);
    $threshold=($threshold)?DB_escape_string($threshold):0;
    $printerName=($printerName)?DB_escape_string($printerName):0;
    $dpt=($dpt)?DB_escape_string($dpt):0;

    // Build the query
    $query="SELECT user,SUM(copies*pages) as total FROM jobs_log ";
    $query.=($printerName)?"WHERE printer=$printerName ":"";
    // For $dpt, let's give up for now, we need to manage user groups...
    // That'll be in a later version :P !
    $query.="GROUP BY user ";
    $query.=($threshold)?"HAVING total => $threshold ":"";
    $query.="ORDER BY total DESC LIMIT $count";

    if($result=DB_query($query)){ //Assignment !
      //return DB_Dump_Result($result);

      $tableULJ=new TBL_table();
      $tableULJ->setCaption("User rankings");
      $tableULJ->setColumns(array('user', 'total'));

      global $jas_userStatsPage;

      while ($row=mysql_fetch_assoc($result)){
        if (isset($jas_userStatsPage))
          $row['user']="<a href=\"".$jas_userStatsPage.$row['user']."\">".$row['user']."</a>";
        $tableULJ->addRow($row);
      }

      mysql_free_result($result);
      return $tableULJ->displayTable('20');
    }
    else{
      $source="jas_getUserRankings";
      $message="Query failed !";
      $hint="Check for the query syntax, and that the MySQL host is up.";
      $severity=ERWARN;
      ER_log_error($source, $message, $severity, $hint);
      return false;
    }
  }

  /* jas_getPrinterRankings: Returns an associative array containing
     the $count most used printers. Optionnaly, filtered by departement
     $dpt. */
  function jas_getPrinterRankings($count, $dpt=""){

    /*$query="SELECT printer,SUM(copies*pages) as total FROM jobs_log ";
    $query.="GROUP BY printer ORDER BY total DESC LIMIT $count";
    $result=DB_query($query);
    DB_dump_result($result);*/

    if (!$count){
      $source="jas_getPrinterRankings";
      $message="Missing number of results to return !";
      $hint="Please specify \$count for this function.";
      $severity=ERWARN;
      ER_log_error($source, $message, $severity, $hint);
      return false;
    }
    // Clean the input variables and prepare query
    $count=DB_escape_string($count);
    $dpt=($dpt)?DB_escape_string($dpt):0;

    // Build the query
    $query="SELECT printer,SUM(copies*pages) as total FROM jobs_log ";
    // For $dpt, let's give up for now, we need to manage user groups...
    // That'll be in a later version :P !
    $query.="GROUP BY printer ORDER BY total DESC LIMIT $count";

    if($result=DB_query($query)){ //Assignment !
      //return DB_Dump_Result($result);

      $tableULJ=new TBL_table();
      $tableULJ->setCaption("Printer rankings");
      $tableULJ->setColumns(array('printer', 'total'));

      global $jas_printerStatsPage;

      while ($row=mysql_fetch_assoc($result)){
        if (isset($jas_printerStatsPage))
          $row['printer']="<a href=\"".$jas_printerStatsPage.$row['printer']."\">".$row['printer']."</a>";
        $tableULJ->addRow($row);
      }

      mysql_free_result($result);
      return $tableULJ->displayTable('20');
    }
    else{
      $source="jas_getPrinterRankings";
      $message="Query failed !";
      $hint="Check for the query syntax, and that the MySQL host is up.";
      $severity=ERWARN;
      ER_log_error($source, $message, $severity, $hint);
      return false;
    }
  }

  /* jas_searchObject: Searches for an object (User, printer...). The
     search string ($string) may contain MySQL jokers, and $objectType
     tells weather to look for printers or users ("printer", "user").
     The result is returned as an array, or 'false' if the query fails,
     and '-1' if no object is found */
  function jas_searchObject($string, $objectType){
    // Check presence of arguments
    if (empty($string) || empty($objectType)){
      $source="jas_searchObject";
      $message="Missing search string and/or object type !";
      $hint="Please specify \$string and \$objectType for this function.";
      $severity=ERWARN;
      ER_log_error($source, $message, $severity, $hint);
      return false;
    }

    // Clean arguments
    $string=DB_escape_string($string, 0);
    $objectType=DB_escape_string($objectType, 0);

    // Check object type value - This code is crappy, I need a way
    // not to hard code "user" and "printer". See below.
    if (!strcmp($objectType,"'user'") || !strcmp($objectType, "'printer'")){
      $source="jas_searchObject";
      $message="Wrong object type !";
      $hint="Please choose either 'printer or 'user' for \$objectType.";
      $severity=ERWARN;
      ER_log_error($source, $message, $severity, $hint);
      return false;
    }

    // Choose the MySQL field to query depending on
    // $objectType. This code is not very extensible and
    // thus is bound to change ;-)
    $queryField=(!strcmp($objectType,"user"))?"user":"printer";

    // build the query
    $query="SELECT $queryField FROM jobs_log WHERE $queryField LIKE";
    $query.=" '%$string%' GROUP BY $queryField ORDER BY $queryField ASC";

    // Run the query and return the result or log an error.
    if($result=DB_query($query)){ //Assignment !
      if (mysql_num_rows($result)){
        while ($line=mysql_fetch_array($result))
          $return_array[]=$line[0];
        return $return_array;
      }
      else
        return -1;
    // TO BE CONTINUED....
    }
    else{
      $source="jas_searchObject";
      $message="Query failed !";
      $hint="Check for the query syntax, and that the MySQL host is up.";
      $severity=ERWARN;
      ER_log_error($source, $message, $severity, $hint);
      return false;
    }
  }
?>
