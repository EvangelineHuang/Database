<?php
/* this function is just used for connecting to psql, inputs are the name of the SQLiteDatabase
the name of the user, and the password of the user. This function will return a pdo object for us to
interact with database if it connects successfully otherwise it will print error message. */
$link=connect_to_psql('genesis', 'xinping_huang', 'xinping_huang');
if($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['logout'])){
    session_destroy();
}

function logoutButton($id){
    echo '<form method="post"><button formaction="logins.php" name="logout" type="submit" value="logout" id="'.$id.'">Log Out</button></form>';
}
function username(){
    echo $_SESSION['username'];
}


function connect_to_psql($db, $name, $password, $verbose=FALSE)
{
  $host = 'localhost';
  $user = $name; // YOU WILL HAVE TO EDIT THESE
  $pass = $password;

  $dsn = "pgsql:host=$host;dbname=$db;user=$user;password=$pass";
  $options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
  ];
  try {
    if ($verbose) {
      debug_message('Connecting to PostgreSQL DB `classes`...', TRUE);
    }
    $pdo = new PDO($dsn, $user, $pass, $options);
    if ($verbose) {
      debug_message('Success!');
    }
    return $pdo;
  } catch (\PDOException $e) {
    debug_message('Error: Could not connect to database! Aborting!');
    debug_message($dsn);
    debug_message($e);
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
  }
}
/*Function to populate shifts table */
function popshift($pdo){
    
    $sql = "SELECT * FROM shifts ORDER BY start_time DESC LIMIT 4";
    try{
        $stmt = $pdo->query($sql);
        debug_message('query successful for shifts table');
    }
    catch (\PDOException $e){
        debug_message('DB query failed. Error: '.$e);
        throw new \PDOException($e->getMessage(), (int)$e->getCode());
    }
    
        
        foreach ($stmt as $row){
            
            $starttime = $row['start_time'];
            $endtime = $row['end_time'];
            $employeeid = $row['employeeid'];
            
            for($i=1;$i<366;$i++){
        
                $n = $i;
        
            //$sqlinsert = "INSERT INTO shifts VALUES (".$row['shiftid'].",".$row['start_time']."+INTERVAL '$i DAY',".$row['end_time']."+INTERVAL '$i DAY',".$row['employeeid'].")";
            $sqlinsert = <<<_SQL_
            
                INSERT INTO shifts (start_time, end_time, employeeID)
                   VALUES 
                    (TIMESTAMP '$starttime' + INTERVAL '$n DAY', TIMESTAMP '$endtime' + INTERVAL '$i DAY', $employeeid)
            
            
_SQL_;

            
            
            try{
                $stmt2=$pdo->query($sqlinsert);
                debug_message('insert successful for shifts table');
            }
             catch (\PDOException $e){
                    debug_message('DB query failed. Error: '.$e);
                    throw new \PDOException($e->getMessage(), (int)$e->getCode());
            }
        }
    
    }
    
}

/* this function will show some debug message but we will not use it a lot */
function debug_message($message, $continued=FALSE)
{
  $html = '<span style="color:orange;">';
  $html .= $message . '</span>';
  if ($continued == FALSE) {
    $html .= '<br />';
  }
  $html .= "\n";
  echo $html;
}
/*the input is the title of the page that will show up on the tab, this function just
set up the header of the html document */
function head($title)
{
  $html=<<<HTML
  <!DOCTYPE html>
  <html>
    <head>
    <link href="https://fonts.googleapis.com/css?family=Satisfy&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Cabin+Condensed&display=swap" rel="stylesheet">
      <link rel="stylesheet" type="text/css" href="style.css">
      <script type="text/javascript" src="https://d3js.org/d3.v5.min.js"></script>
      <title>$title</title>
    </head>
    <body>
HTML;
   echo $html;
}
/*this function is similar to the head() function but it will set up the footer of the page. */
function foot()
{
  $html=<<<HTML
</body>
</html>
HTML;
 echo $html;
}
/* this function will create a drop down box for all the students in the database */
function DropDownForm($pdo){
    echo '<form method="post">';
    echo '<span>Student Name: </span><div class="outer"><div class="dropdown"><select name="student" id="student">';
    echo '<option value="alls">Select All</option>';
    DropDownStudents($pdo);
    echo '</select></div></div><br>';
    echo '<span>Proctor Name: </span><div class="outer"><div class="dropdown"><select name="employee" id="employee">';
    echo '<option value="alle">Select All</option>';
    DropDownEmployees($pdo);
    echo '</select></div></div><br>';
    echo '<span>Instructor Name: </span><div class="outer"><div class="dropdown"><select name="instructor" id="instructor">';
    echo '<option value="alli">Select All</option>';
    DropDownInstructors($pdo);
    echo '</select></div></div><br>';
    echo '<input type="submit" value="Confirm" name="submit" class="submit">';
    echo '</form>';
}
if($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['student']) and isset($_POST['employee']) and isset($_POST['instructor']))
   {
       generateReport($link,$_POST['student'],$_POST['employee'],$_POST['instructor']);
   }
function generateReport($pdo,$student,$employee,$instructor){
    if ($student=='alls' and $employee=='alle' and $instructor=='alli'){
        $sql="SELECT student_first_name || ' ' || student_last_name AS student, employee_fname || ' ' || employee_lname AS proctor, 
                     instructor_first_name || ' ' || instructor_last_name AS professor, test_status AS status, test_type AS type, 
                     scheduled_start_time AS schedule, actual_start_time AS start, actual_end_time AS end, actual_end_time-actual_start_time AS duration,
                     seat_id AS seat
                FROM students  
                     INNER JOIN tests USING (student_id)
                     INNER JOIN employees USING (employeeID)
                     INNER JOIN instructors USING (instructor_id)
                     INNER JOIN seats USING (seat_id);";
        $stmt=$pdo->query($sql);
    }
    elseif ($student !='alls' and $employee=='alle' and $instructor=='alli'){
        $sql="SELECT student_first_name || ' ' || student_last_name AS student, employee_fname || ' ' || employee_lname AS proctor, 
                     instructor_first_name || ' ' || instructor_last_name AS professor, test_status AS status, test_type AS type, 
                     scheduled_start_time AS schedule, actual_start_time AS start, actual_end_time AS end, actual_end_time-actual_start_time AS duration,
                     seat_id AS seat
                FROM students  
                     INNER JOIN tests USING (student_id)
                     INNER JOIN employees USING (employeeID)
                     INNER JOIN instructors USING (instructor_id)
                     INNER JOIN seats USING (seat_id)
               WHERE students.student_id = ?;";
        $stmt=$pdo->prepare($sql);
        $stmt->execute([$student]);
    }
    elseif ($student =='alls' and $employee !='alle' and $instructor =='alli'){
        $sql="SELECT student_first_name || ' ' || student_last_name AS student, employee_fname || ' ' || employee_lname AS proctor, 
                     instructor_first_name || ' ' || instructor_last_name AS professor, test_status AS status, test_type AS type, 
                     scheduled_start_time AS schedule, actual_start_time AS start, actual_end_time AS end, actual_end_time-actual_start_time AS duration,
                     seat_id AS seat
                FROM students  
                     INNER JOIN tests USING (student_id)
                     INNER JOIN employees USING (employeeID)
                     INNER JOIN instructors USING (instructor_id)
                     INNER JOIN seats USING (seat_id)
               WHERE employees.employeeID = ?;";
        $stmt=$pdo->prepare($sql);
        $stmt->execute([$employee]);
    }
    elseif ($student =='alls' and $employee=='alle' and $instructor !='alli'){
        $sql="SELECT student_first_name || ' ' || student_last_name AS student, employee_fname || ' ' || employee_lname AS proctor, 
                     instructor_first_name || ' ' || instructor_last_name AS professor, test_status AS status, test_type AS type, 
                     scheduled_start_time AS schedule, actual_start_time AS start, actual_end_time AS end, actual_end_time-actual_start_time AS duration,
                     seat_id AS seat
                FROM students  
                     INNER JOIN tests USING (student_id)
                     INNER JOIN employees USING (employeeID)
                     INNER JOIN instructors USING (instructor_id)
                     INNER JOIN seats USING (seat_id)
               WHERE instructors.instructor_id = ?;";
        $stmt=$pdo->prepare($sql);
        $stmt->execute([$instructor]);
    }
    elseif ($student !='alls' and $employee !='alle' and $instructor=='alli'){
        $sql="SELECT student_first_name || ' ' || student_last_name AS student, employee_fname || ' ' || employee_lname AS proctor, 
                     instructor_first_name || ' ' || instructor_last_name AS professor, test_status AS status, test_type AS type, 
                     scheduled_start_time AS schedule, actual_start_time AS start, actual_end_time AS end, actual_end_time-actual_start_time AS duration,
                     seat_id AS seat
                FROM students  
                     INNER JOIN tests USING (student_id)
                     INNER JOIN employees USING (employeeID)
                     INNER JOIN instructors USING (instructor_id)
                     INNER JOIN seats USING (seat_id)
               WHERE students.student_id = ?
                 AND employees.employeeID=?;";
        $stmt=$pdo->prepare($sql);
        $stmt->execute([$student,$employee]);
    }
    elseif ($student !='alls' and $employee =='alle' and $instructor !='alli'){
        $sql="SELECT student_first_name || ' ' || student_last_name AS student, employee_fname || ' ' || employee_lname AS proctor, 
                     instructor_first_name || ' ' || instructor_last_name AS professor, test_status AS status, test_type AS type, 
                     scheduled_start_time AS schedule, actual_start_time AS start, actual_end_time AS end, actual_end_time-actual_start_time AS duration,
                     seat_id AS seat
                FROM students  
                     INNER JOIN tests USING (student_id)
                     INNER JOIN employees USING (employeeID)
                     INNER JOIN instructors USING (instructor_id)
                     INNER JOIN seats USING (seat_id)
               WHERE students.student_id = ?
                 AND instructors.instructor_id=?;";
        $stmt=$pdo->prepare($sql);
        $stmt->execute([$student,$instructor]);
    }
    elseif ($student =='alls' and $employee !='alle' and $instructor !='alli'){
        $sql="SELECT student_first_name || ' ' || student_last_name AS student, employee_fname || ' ' || employee_lname AS proctor, 
                     instructor_first_name || ' ' || instructor_last_name AS professor, test_status AS status, test_type AS type, 
                     scheduled_start_time AS schedule, actual_start_time AS start, actual_end_time AS end, actual_end_time-actual_start_time AS duration,
                     seat_id AS seat
                FROM students  
                     INNER JOIN tests USING (student_id)
                     INNER JOIN employees USING (employeeID)
                     INNER JOIN instructors USING (instructor_id)
                     INNER JOIN seats USING (seat_id)
               WHERE instructors.instructor_id = ?
                 AND employees.employeeID=?;";
        $stmt=$pdo->prepare($sql);
        $stmt->execute([$instructor,$employee]);
    }
    elseif ($student !='alls' and $employee !='alle' and $instructor !='alli'){
        $sql="SELECT student_first_name || ' ' || student_last_name AS student, employee_fname || ' ' || employee_lname AS proctor, 
                     instructor_first_name || ' ' || instructor_last_name AS professor, test_status AS status, test_type AS type, 
                     scheduled_start_time AS schedule, actual_start_time AS start, actual_end_time AS end, actual_end_time-actual_start_time AS duration,
                     seat_id AS seat
                FROM students  
                     INNER JOIN tests USING (student_id)
                     INNER JOIN employees USING (employeeID)
                     INNER JOIN instructors USING (instructor_id)
                     INNER JOIN seats USING (seat_id)
               WHERE students.student_id = ?
                 AND employees.employeeID=?
                 AND instructors.instructor_id=?;";
        $stmt=$pdo->prepare($sql);
        $stmt->execute([$student,$employee,$instructor]);
    }
      echo '<div id="rpt"><table id="report">';
      echo '<tr>';
      echo "<th>Student</th>";
      echo "<th>Proctor</th>";
      echo "<th>Professort</th>";
      echo "<th>Test Status</th>";
      echo "<th>Test Type</th>";
      echo "<th>Scheduled Time</th>";
      echo "<th>Start Time</th>";
      echo "<th>End Time</th>";
      echo "<th>Test Duration</th>";
      echo "<th>Seat</th>";
      echo '</tr>';
      while ($row = $stmt->fetch()){
         echo '<tr>';
         echo "<td>".$row['student']."</td>";
         echo "<td>".$row['proctor']."</td>";
         echo "<td>".$row['professor']."</td>";
         echo "<td>".$row['status']."</td>";
         echo "<td>".$row['type']."</td>";
         echo "<td>".$row['schedule']."</td>";
         echo "<td>".$row['start']."</td>";
         echo "<td>".$row['end']."</td>";
         echo "<td>".$row['duration']."</td>";
         echo "<td>".$row['seat']."</td>";
         echo '</tr>';
}
      echo '</table></div>';

}
function DropDownStudents($pdo){
    $stmt = $pdo->query("SELECT student_first_name || ' ' || student_last_name AS student_name, student_id
                           FROM students;");
    while ($row = $stmt->fetch()){
        echo '<option value="'.$row['student_id'].'">'.$row["student_name"].'</option>';
    }
}
/* this function will create a drop down box for all the instructors in the database */
function DropDownInstructors($pdo){
    $stmt = $pdo->query("SELECT instructor_first_name || ' ' || instructor_last_name AS instructor_name, instructor_id
                           FROM instructors;");       
    while ($row = $stmt->fetch()){
        echo '<option value="'.$row['instructor_id'].'">'.$row['instructor_name'].'</option>';
    }
}
/* this function will create a drop down box for all the employees in the database */
function DropDownEmployees($pdo){
    $stmt = $pdo->query("SELECT employee_fname || ' ' || employee_lname AS employee_name, employeeID AS employee_id
                           FROM employees;");
    while ($row = $stmt->fetch()){
        echo '<option value="'.$row['employee_id'].'">'.$row['employee_name'].'</option>';
    }

}
/*this function will create a table that display all the schedules of the current day for employees */
function CurrentSchedule($pdo){
  $stmt=$pdo->query("SELECT student_id, seat_id, employeeID AS employee_id, instructor_id, students.student_first_name || ' ' || students.student_last_name AS student_name, seat_id, employee_fname || ' ' || employee_lname AS employee_name,
                            scheduled_start_time AS start_time, (CASE delivered WHEN TRUE THEN 'YES'
                                                                                WHEN FALSE THEN 'NO'
                                                                                END) AS delivered
                       FROM tests 
                       INNER JOIN students USING (student_id)
                       INNER JOIN employees USING (employeeID)
                      WHERE DATE(scheduled_start_time) = current_date
                      ORDER BY scheduled_start_time;");
  echo '<h1>Current Schedule</h1>';
  echo '<table id="schedule">';
  echo '<tr>';
  echo '<th>Student Name</th>';
  echo '<th>Seat</th>';
  echo '<th>Proctor</th>';
  echo '<th>Start Time</th>';
  echo '<th>Delivery</th>';
  echo '<th>Edit</th>';
  echo '</tr>';

  while ($row = $stmt->fetch()){
      echo '<tr>';
      echo '<td>'.$row['student_name'].'</td>';
      echo '<td>'.$row['seat_id'].'</td>';
      echo '<td>'.$row['employee_name'].'</td>';
      echo '<td>'.$row['start_time'].'</td>';
      echo '<td>'.$row['delivered'].'</td>';
      echo '<td><form action="edit_form.php" method="post"><input type="hidden" name="edit" value="'.$row['student_id'].','.$row['instructor_id'].','.$row["employee_id"].','.$row['seat_id'].','.$row['start_time'].'">';
      echo '<input type="submit" name="submit" value="Edit"></form></td>';
      echo '</tr>';
  }
  echo '</form>';
}

function CurrentInsSchedule($pdo,$id){
    
    
  $sql = "SELECT student_id, seat_id, employeeID AS employee_id, instructor_id, students.student_first_name || ' ' || students.student_last_name AS student_name, seat_id, employee_fname || ' ' || employee_lname AS employee_name,
                            scheduled_start_time AS start_time, actual_start_time, actual_end_time, (CASE delivered WHEN TRUE THEN 'YES'
                                                                                WHEN FALSE THEN 'NO'
                                                                                END) AS delivered
                       FROM tests 
                       INNER JOIN students USING (student_id)
                       INNER JOIN employees USING (employeeID)
		      WHERE  instructor_id =  ?
		        AND scheduled_start_time>=current_date
                      ORDER BY scheduled_start_time;";
                      
  
  $stmt = $pdo->prepare($sql);
  
  $d = [$id];
  
  $stmt->execute($d);
  
  echo '<h1>Current Schedule</h1>';
  echo '<table id="schedule">';
  echo '<tr>';
  echo '<th>Student Name</th>';
  echo '<th>Seat</th>';
  echo '<th>Proctor</th>';
  echo '<th>Scheduled Start Time</th>';
  echo '<th>Actual Start Time</th>';
  echo '<th>Time Ended</th>';
  echo '<th>Delivery</th>';
  echo '<th>Edit</th>';
  echo '</tr>';

  while ($row = $stmt->fetch()){
      echo '<tr>';
      echo '<td>'.$row['student_name'].'</td>';
      echo '<td>'.$row['seat_id'].'</td>';
      echo '<td>'.$row['employee_name'].'</td>';
      echo '<td>'.$row['start_time'].'</td>';
      echo '<td>'.$row['actual_start_time'].'</td>';
      echo '<td>'.$row['actual_end_time'].'</td>';
      echo '<td>'.$row['delivered'].'</td>';
      $today = date("Y-m-d h:i:s");
      $now = new DateTime("$today");
      $start = new DateTime($row['start_time']);
      $interval = date_diff($now,$start);
      $days=$interval->format("%d");
      $hours = $interval->format("%h");
      if($days!=0){
      echo '<td><form action="edit_form.php" method="post"><input type="hidden" name="edit" value="'.$row['student_id'].','.$row['instructor_id'].','.$row["employee_id"].','.$row['seat_id'].','.$row['start_time'].'">';
      echo '<input type="submit" name="submit" value="Edit"></form></td>';}
      else{
      echo '<td>Cannot edit within 24 hours</td>';

      }
      echo '</tr>';}
  echo '</form>';
}

function links($link,$name){
  echo "<a href=".$link."><button id=".$name.">$name</button></a>";
}
