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

     /* libTables.php: Functions to generate and manage html tables */

include_once("libError.php");
     
  /* Class to handle html tables, display them, sort them...
  
    table::setColumns(array $headers) : sets the table's columns number and names,
      from an array containing the columns' names.
    table::setCaption($caption) : Sets the table's caption.
    table::addRow(array values) : Adds a row to the table, from an array containing values.
    table::displayHeaders() : Displays the html table's headers (<th>).
    table::displayRow($rowNumber) : Displays a given row of the html table.
    table::displayTable($headerRepeat) : Displays the whole table. Writes the header
      row each $headerRepeat data row, or once if equal to 0 or not specified.
    NOTE: For the last 3 functions, if $print is set to 1, the result is immediately
          printed. If set to 0, it is return to the calling function as a string.

    table::caption : Contains the table's caption. 
    table::rowsNumber : Number of rows in the table.
    table::columnsNumber : Number of columns in the table.
    table::columns : Array containing the headers' names.
    table::rows : Array containing the rows arrays.
  */ 
  class TBL_table {
    // Class variables
    var $columnsNumber;
    var $rowsNumber;
    var $caption;
    var $columns=array();
    var $rows=array();

    // Class functions
    function setColumns($columns){
      if (empty ($columns) || !is_array($columns)){
	// Error
        $source="table::setColumns()";
        $message="Wrong or missing columns values !";
        $hint="Please provide the \$columns array for this function.";
        $severity=ERWARN;
        ER_log_error($source, $message, $severity, $hint);
	return false;
      }
      else {
        $this->columns=$columns;
	$this->columnsNumber=count($columns);
      }
    }

    function addRow($row){
      if (empty ($row) || !is_array($row)){
        // Error !!!
        $source="table::addRow()";
        $message="Wrong or missing row array !";
        $hint="Please provide the \$row array for this function.";
        $severity=ERWARN;
        ER_log_error($source, $message, $severity, $hint);
        return false;
      }
      else {
        $this->rows[]=$row;
      }
    }
    
    function setCaption($caption){
      if (empty($caption)){
        // Error
        $source="table::setCaption()";
        $message="Missing caption !";
        $hint="Please provide \$caption for this function.";
        $severity=ERWARN;
        ER_log_error($source, $message, $severity, $hint);
	return false;
      }
      $this->caption=$caption;
    }
    
    function displayRow($rowNumber, $print=0){
      if (!is_array($this->rows[$rowNumber])){
        //  Error
        $source="table::displayRow()";
        $message="Missing row number !";
        $hint="Please provide \$rowNumber for this function.";
        $severity=ERWARN;
        ER_log_error($source, $message, $severity, $hint);
	return false;
      }
      $output="  <tr>\n";
      foreach($this->rows[$rowNumber] as $field){
        $output.="    <td>";
	$output.=(!empty($field))?$field:"&nbsp;";
	$output.= "</td>\n";
      }
      $output.="  </tr>\n";
      if ($print){
        echo $output;
	return true;
      }
      return $output;
    }

    function displayHeaders($print=0){
      $output="  <tr>\n";
      foreach($this->columns as $field){
        $output.="    <th>";
        $output.=(!empty($field))?$field:"&nbsp;";
        $output.="</th>\n";
      }
      $output.="  </tr>\n";
      if ($print){
        echo $output;
	return true;
      }
      return $output;
    }

    function displayTable($headerRepeat=0, $print=0){
      $output="<!-- Begin table -->\n";
      $output.="<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\">\n";
      $output.="<caption>".$this->caption."</caption>\n";
      $output.=$this->displayHeaders($print);
      for ($rowNumber=0; $rowNumber<count($this->rows); $rowNumber++){
	if (($headerRepeat!=0) && ($rowNumber!=0)){
	  if (($rowNumber % $headerRepeat)==0)
	    $output.=$this->displayHeaders($print);
	}
        $output.=$this->displayRow($rowNumber, $print);
      }
      $output.="</table>\n";
      $output.="<!-- End table -->\n";
      if ($print){
        echo $output;
	return true;
      }
      return $output;
    }
  }
?>
