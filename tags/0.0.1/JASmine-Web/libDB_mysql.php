<?php
  /* libDB_mysql.php: Provides abstraction functions to deal
     with Mysql servers.
       Version 0-09.05.2005 */

  include_once("libError.php");

  // Opens a connection to a Mysql server, and returns the connection ID
  function DB_connect($host,$user,$pass){
    if ($id=@mysql_connect($host,$user,$pass)) // Assignment !
      return $id;
    else {
      $message="Unable to connect to host \"$host\": ".mysql_error();
      $hint="Check that the MySQL host is up, and that you gave the right hostname.";
      ER_log_error("DB_connect", $message, ERCRIT, $hint);
      return false;
    }
  }

  /* Selects a database on the current connection ID. Returns true
     or false depending on the result */
  function DB_select($db){
    if (@mysql_select_db($db))
      return true;
    else {
      $message="Unable to select database \"$db\": ".mysql_error();
      $hint="Check that this database exists, and that you gave the right name.";
      ER_log_error("DB_select", $message, ERCRIT, $hint);
      return false;
    }
  }

  // Runs a query and returns the result ID
  function DB_query($query){
    if ($result=@mysql_query($query)) // Assignment !
      return $result;
    else {
      $message="Unable to run query \"$query\": ".mysql_error();
      $hint="Check the syntax of this query, and that the requested data exists.";
      ER_log_error("DB_query", $message, ERCRIT, $hint);
      return false;
    }
  }

  /* This function returns the result of a query as an HTML table.
     Content and format of the result are guessed automatically. 
     Alternatively, the result can be printed immediately, not returned,
     based on the value of the $print variable (0 <=> "return a string") */
  function DB_dump_result($result, $print=0){
    if (! $result)
      return false;
    if (! $fields_nb=mysql_num_fields($result))
      return false;

    $output="<!-- Starting query result dump  -->\n";
    //$output.="<p>\n";	//PAS XHTML VALIDE !!
    $output.="  <table border=\"1\" cellpadding=\"2\" cellspacing=\"0\">\n";
    $output.="    <tr>\n";

    for ($i=0; $i<$fields_nb; $i++){
      $field_names[$i]=mysql_field_name($result, $i);
      $output.="      <th>$field_names[$i]</th>\n";
    }

    $output.="  </tr>\n";

    while ($row=mysql_fetch_array($result, MYSQL_ASSOC)){
      $output.="    <tr>\n";
      foreach($field_names as $this_field){  // foreach($row) does not work here without "MYSQL_ASSOC"
        $output.=($row[$this_field])?"      <td>$row[$this_field]</td>\n":"<td>&nbsp;</td>\n";
      }
      $output.="    </tr>\n";
    }

    $output.="  </table>\n";
    // $output.="</p>\n"; // PAS XHTML VALIDE !
    $output.="<!-- End query result dump -->\n";

    // Finally output the result, with either method, based
    // on the coder's choice.
    if ($print){
      echo $output;
    }
    return $output;
  }

  /* Function that generate an HTML dropdown list from a query result and the names of two fields of this query
     (Text to display and corresponding ID). One can too provide the current value of that field, if known,
     So that this value is selected as default in the list (Useful for example when displaying a form to modify an entry...) */
  function DB_dropdown_list($result, $text_field, $id_field, $current_value=""){
    $output="<select name=i_$text_field>\n";
    while ($row=mysql_fetch_array($result, MYSQL_ASSOC)){
      if ($current_value==$row[$id_field])
        $selected=" selected";
      else
        $selected="";
       $output.="  <option value=$row[$id_field]".$selected.">".stripslashes($row[$text_field])."</option>\n";
    }
    $output.="</select>\n";
    return $output;
  }

  /* Function to clean inputs defore querying a database. Mandatory
     to protect the project from "SQL injections"
     To unescape the string, "stripslashes()" is enough.
     Optionnally, one can request this function not to single-quote
     the result, by setting $quote to 'true'.
     This function was stolen from http://php.net examples ;-) */     
  function DB_escape_string($string, $quote=1){
    // Stripslashes if slashes already present.
    if (get_magic_quotes_gpc()) {
      $string = stripslashes($string);
    }
    // Escape if not integer value.
    if (!is_numeric($string)) {
      // This one will fail if no connection to the SQL server, so:
      if($string=@mysql_real_escape_string($string)){ // Assignment !!!
        $string=($quote==true)?"'$string'":$string;
      }
      else{
        $string="'".mysql_escape_string($string)."'";
        $message="Unable to real_escape string: ".mysql_error();
        $hint="This happens when the MySQL server cannot be reached: Check that it is up !";
        ER_log_error("DB_escape_string", $message, ERWARN, $hint);
      }
    }
    return $string;
  }
?>