<?php session_start(); ?>
<?php
/* this page is the home page of employees after they log in.
The schedules of all the students of that day will be displayed.*/
      require('function.php');
      /*debug_message('Program starts');*/
      $pdo=connect_to_psql('genesis', 'xinping_huang', 'xinping_huang');
?>
<?php head('Employee Homepage');
      echo '<div id="tschedule">';
      CurrentSchedule($pdo);
      echo '</div>';
      echo '<nav class="menu" tabindex="0">';
      echo '<ul>';
      echo '<li class="nohover">';
      echo '<img src="unknown-user.png" alt="No Image Upload">';
      echo '</li>';
      echo '<li class="nohover username">';
      username();
      echo '</li>';
      echo '<li>';
      links('report.php','Report');
      echo '</li>';
      echo '<li>';
      links('register_form.php','Register');
      echo '</li>';
      echo '<li>';
      links('shifts.php','Shifts');
      echo '</li>';
      echo '<li>';
      logoutButton('emlogout');
      echo '</li>';
      echo '</ul>';
      foot();?>

