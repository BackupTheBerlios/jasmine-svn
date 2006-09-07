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

  /* libError.php: This library provides functions to handle errors
     occuring in the scripts, and display them properly.
       Version 0-29.04.2005
  */

  /* Constants: Describe the severity of an error. */
  define ("ERINFO", 1);
  define ("ERWARN", 2);
  define ("ERCRIT", 3);

  /* This array lists all the above constants, for checking
     during calls to ER_* fonctions, and translation. */
  $ER_crit_array=array(
    ERINFO => "Info",
    ERWARN => "Warning",
    ERCRIT => "Critical"
  );  // Penser à mettre des gettext() sur ces lignes !

  /* The $ER_errors_to_display[] array will contain the errors to display. It will be
     parsed by the ER_display_errors(). It is composed of arrays
     containing error messages for each severity level. */
  global $ER_errors_to_display;

  /* ER_init_array: Inits (And thus clears) the error global array */
  function ER_init_array(){    
    global $ER_errors_to_display;
    $ER_errors_to_display = array(
                                 ERINFO => array(),
                                 ERWARN => array(),
                                 ERCRIT => array()
                            );
  }

  //Well, use it, just to be sure :
  ER_init_array();

  /* ER_clear_log: Used to clear the error log, in order to start
     a new one... Well, same code as above, so make only a wrapper ! */
  function ER_clear_log(){
    ER_init_array();
  }

  /* Function to diplay all the errors that occured in the
     scripts, sorted by severity. */
  function ER_display_errors(){
    global $ER_errors_to_display;

    /*debugging !
    // array($source, $message, $severity, $hint)
    ?>
    <pre>
    <?php
      print_r($ER_crit_array);
      print_r($ER_errors_to_display);
    ?>
    </pre>
    <?php*/

    // Count the number of errors for each type
    $nb_crit=count($ER_errors_to_display[ERCRIT]);
    $nb_warn=count($ER_errors_to_display[ERWARN]);
    $nb_info=count($ER_errors_to_display[ERINFO]);

    //echo "$nb_crit, $nb_warn, $nb_info\n"; //DEBUG

    // If no message was registered, do nothing
    if (($nb_crit + $nb_warn + $nb_info) == 0)
      return 1;

    // Begin the error block
    echo "<!-- Begin error block -->\n";
    echo "<div class=\"error_block\">\n";
    echo "  <h2>Informations</h2>\n";

    // Display critical errors, if any
    if ($nb_crit){
      echo "  <!-- Critical errors occured -->\n";
      echo "  <h3>Critical errors</h3>\n";
      echo "  <dl>\n";
      foreach($ER_errors_to_display[ERCRIT] as $an_error){
        echo "    <dt>\n";
        echo "      <strong>".$an_error[0]."</strong>\n";
        echo "      ".$an_error[1]."\n";
        echo "      <dd>".$an_error[2]."</dd>\n";
        echo "    </dt>\n";
      }
      echo "  </dl>\n";
    }

    // Display warnings, if any
    if ($nb_warn){
      echo "  <!-- Warnings occured -->\n";
      echo "  <h3>Warnings</h3>\n";
      echo "  <dl>\n";
      foreach($ER_errors_to_display[ERWARN] as $an_error){
        echo "    <dt>\n";
        echo "      <strong>".$an_error[0]."</strong>\n";
        echo "      ".$an_error[1]."\n";
        echo "      <dd>".$an_error[2]."</dd>\n";
        echo "    </dt>\n";
      }
      echo "  </dl>\n";
    }

    // Display infos, if any
    if ($nb_info){
      echo "  <!-- Informative messages -->\n";
      echo "  <h3>Infomative messages</h3>\n";
      echo "  <dl>\n";
      foreach($ER_errors_to_display[ERINFO] as $an_error){
        echo "    <dt>\n";
        echo "      <strong>".$an_error[0]."</strong>\n";
        echo "      ".$an_error[1]."\n";
        echo "      <dd>".$an_error[2]."</dd>\n";
        echo "    </dt>\n";
      }
      echo "  </dl>\n";
    }
    echo "</div>\n";
    echo "<!-- End error block -->\n";
  }

  /* ER_log_error: Logs an error for later displaying. $source
     must describe the function or section throwing the error, 
     $message describes the error, $severity is one of the constants
     defined on top of this file, and hint is an optional hint to
     give to the user, for example to correct the problem.
   */
  function ER_log_error($source, $message, $severity, $hint=""){
    // Check presence of mandatory args
    if (empty($source) || empty($message) || empty($severity)){
      $tmpmsg="Cannot display error, wrong format.";
      $tmphint="Check how you called ER_log_error() !";
      ER_log_error("ER_log_error", $tmpmsg, ERWARN, $tmphint);
      return 0;
    }

    // Get the list of severities in our scope
    global $ER_crit_array;
    // Check validity of the criticity parameter (See top of
    // this file)
    if (!array_key_exists($severity, $ER_crit_array)){
      $tmpmsg="Cannot display error, wrong severity.";
      $tmphint="Check how you called ER_log_error() !";
      ER_log_error("ER_log_error", $tmpmsg, ERWARN, $tmphint);
      return 0;
    }

    // Get the global array in our scope
    global $ER_errors_to_display;

    // Clean the messages
    $source=htmlentities($source);
    $message=htmlentities($message);
    $hint=htmlentities($hint);

    // Finally, add the error message to the global array for later
    // displaying by ER_display_errors() !
    $ER_errors_to_display[$severity][]=array($source, $message, $hint);
  }
?>
