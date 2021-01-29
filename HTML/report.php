<?php
/* this page is the home page of employees after they log in.
The schedules of all the students of that day will be displayed.*/
      require('function.php');
      /*debug_message('Program starts');*/
      $pdo=connect_to_psql('genesis', 'xinping_huang', 'xinping_huang');


head('Report');
DropDownForm($pdo);
links('employee_home.php','Homepage');
foot();
