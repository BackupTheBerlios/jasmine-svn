<?php
  /* Menu.php: Displays the main menu
       Version: Version: 0-20.05.2005 */
       
       
?>    <div class="menu">
      <h2>Menu</h2>
      <ul>
        <li id="menu_general">
	  <span title="Main section">Main</span>
          <ul>
            <li>
              <a href="index.php" title="Display the main page">Main page</a>
            </li>
	    <li>
	      <a href="index.php?section=help" title="Help section">Help</a>
	    </li>
	    <li>	      
          </ul>
	</li>
        <li id="menu_reports">
          <span title="Reports section">Reports</span>
          <ul>
            <li>
              <a href="index.php?section=summary" title="Display the summary">Summary</a>
            </li>
          </ul>
        </li>
	<li id="menu_find">
          <span title="Find a report or object">Search</span>
          <ul>
            <li>
              <a href="index.php?section=find&amp;searchType=printer" title="Seek a printer">Printers</a>
            </li>
	    <li>
              <a href="index.php?section=find&amp;searchType=user" title="Seek a user">Users</a>
            </li>
          </ul>
        </li>
      </ul>
    </div>
