<?php
/* libJasReports.php: Stores functions related to JASmine reports,
   such as printer stats, user stats, etc.... 
     Version 0-27.05.2005 */

   include_once("libDB_mysql.php");
   include_once("libError.php");

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

    $query="SELECT date,title,(copies*pages) as total FROM jobs_log ";
    $query.="WHERE DATE_SUB(CURDATE(),INTERVAL $days DAY) <= date AND user=$userName ";

    if (!empty($printerName)){
      $printerName=DB_escape_string($printerName);
      $query.="AND printer=$printerName ";
    }

    $query.="ORDER BY DATE DESC";

    if($result=DB_query($query)){ //Assignment !
      return DB_Dump_Result($result);

      // TO BE CONTINUED....
      /*$row=mysql_fetch_row($result);
      mysql_free_result($result);
      if (!empty($row[0]))
        return $row[0];
      else
        return 0;*/
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
      return DB_Dump_Result($result);

    // TO BE CONTINUED....
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
      return DB_Dump_Result($result);

    // TO BE CONTINUED....
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