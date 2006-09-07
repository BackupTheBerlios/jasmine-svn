<?php
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

