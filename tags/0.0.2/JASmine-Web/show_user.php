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

  /* Show_user.php: Displays stats for a given user, 
     passed with $_GET['user']. */
     
  // Includes
  include_once("libJasReports.php");
  

  // Connect to the DB
  DB_connect($DB_host,$DB_login,$DB_pass);
  DB_select($DB_db);
  
  // Get the username
  $user=$_GET['user'];
  
  // Get some stats
  $userTotalPages=jas_getUserTotalPages($user);
  
  // Get user's last month history
  $userJobHistory=jas_getUserLastJobs($user, 30)
  
?>
    <!-- Begin user stats -->
      <h2>Stats for user "<?php echo $user; ?>"</h2>
      <p>
        <em>Here are some stats for <strong><?php echo $user; ?></strong>: First,
          display all time total number of pages printed by this
          user, then list the jobs printed within the last 30 days.</em>
      </p>
      <h3>Total number of pages</h3>
      <p>
        <em><?php echo $user; ?> has printed <?php echo $userTotalPages; ?> pages.</em>
      </p>
      <h3>Last 30 days history</h3>
      <?php echo $userJobHistory; ?>
    <!-- End user stats -->

