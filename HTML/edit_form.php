<!-- Creator: Courtney Gibson -->
<?php session_start() ?>
<?php
	require('function.php');

function buildForm($pdo, $stu_id, $ins_id, $emp_id, $s_id, $start_time)
{
    echo '<br />';
    
    /* select instructor */
    echo "<form method='post'>";
    echo"<input type='hidden' value='$stu_id' name='stuid'>";
    echo"<input type='hidden' value='$ins_id' name='insid'>";
    echo"<input type='hidden' value='$emp_id' name='empid'>";
    echo "<input type='hidden' value='$s_id' name='sid'>";
    echo "<input type='hidden' value='$start_time' name='stime'>";
    //
       /*if (*/
	$insNameSQL = 'SELECT instructor_first_name || instructor_last_name AS name
			FROM instructors
			WHERE instructor_id = '.$ins_id.';';
    $stmt = $pdo->query($insNameSQL);
    foreach($stmt as $row)
    {	
	  $insName = $row['name'];
	  echo "<p>Instructor: $insName </p>";
    }
	
  
	/* select student */
	
    $stuNameSQL = 'SELECT student_first_name || student_last_name AS name
			FROM students
			WHERE student_id = '.$stu_id.';';
    $stmt = $pdo->query($stuNameSQL);
    foreach($stmt as $row)
    {

	$stuName = $row['name'];
	echo "<p>Student: $stuName</p>";

    }

        /* test date */
    echo '<p> Select Test Date: </p>';
    echo "<div class='outer'>";
    echo "<div class='dropdown'>";
    $date=date('Y-m-d');
    echo '<input type="date" name="pickday" min="'.$date.'" value="'.$date.'">';
    /*$stmt = $pdo->query("SELECT DISTINCT date(start_time) AS day
	    			FROM shifts
				WHERE start_time > NOW()
				ORDER BY day ASC;");
	while ($row = $stmt->fetch())
	{
		echo "<option value='".$row['day']."'>".$row['day']."</option>";
	}	
	echo '</select>';*/
	echo "</div>";
        echo "</div>";
    
    /* test start time */
	echo '<p>Select Test Start Time: </p>';
	echo "<div class='outer'>";
        echo "<div class='dropdown'>";
	echo "<select name='pickTime'>";

	$start = new DateTime("$start_time");	
	$startT = $start->format('H:i:s');
	echo "<option value=".$startT.">$startT</option>";



	$timeList = [];
	$beginTime = date_create("08:00:00");

	for ($i = 0; $i<= 48; $i++)
	{
		date_add($beginTime, date_interval_create_from_date_string("10 minutes"));
		date_format($beginTime, 'H:i:s');
		$newT = $beginTime->format('H:i:s');
		array_push($timeList, $newT);
		
		
	//	$bTime = $beginTime->format('H:i');
	//	echo "<p>".$bTime."</p>";
	}

	foreach($timeList as $time)
	{
	//	$optionTime = $time->format('H:i');
		echo "<option value= '$time' >".$time."</option>";
	}
	echo"</select>";
	echo "</div>";
        echo "</div>";
    
    /* test end time, duration will be calculated */
	echo '<p> Select Test Duration (Minutes): </p>'; /* calculate duration to be stored */
	echo "<div class='outer'>";
        echo "<div class='dropdown'>";
	echo '<select name="duration">';
	
	$durList = [10, 20, 30, 40, 50, 60, 70, 80, 90, 100, 110, 120, 130, 140, 150, 160, 170, 180, 190, 200, 210, 220, 230, 240];

	foreach($durList as $dur)
	{
	      echo "<option>".$dur."</option>";
	}

	echo '</select>';
	echo "</div>";
        echo "</div>";
    
    /* test type */
	echo '<p> Select Test Type: </p>';
	echo "<div class='outer'>";
        echo "<div class='dropdown'>";
        echo '<select name="type">';
        $testTypes = array("Computer", "Paper (via Email)", "Paper (hard copy)");
    
        echo '<option>Computer</option>';
        echo '<option>Paper (via Email)</option>';
        echo '<option>Paper (hard copy)</option>';
        
        echo '</select>';
        echo "</div>";
        echo "</div>";
	       
	echo "<br />"; 
	$current_time=date("H:i:s");
    	if($_SESSION['utype']==2){
        echo"<p>Time Started</p> <input type='time'  name='actStart' min='08:00' max='16:50' value='$current_time'>";
        echo"<p>Time Ended</p> <input type='time'  name='actEnd' min='08:10' max='17:0'>";
	}
        echo "<input type='hidden' name='delivered' value='false'>";
        echo"\n<p>Test Delivered</p><input type='checkbox' name='delivered' value='true'>";
	echo "</br>";
        echo"\n<input type='submit' value='update' name='update'>";
        echo "<input type='submit' value='delete' name='delete'>";
	echo "</form>";
	
}

function update($pdo)
{

	$startday = $_POST['pickday']." ".$_POST['pickTime'];
	$startdate = new DateTime("$startday");

	$duration = $_POST['duration'];

	$calcend = $startdate;
	$calcend -> add(new DateInterval('PT'.$duration.'M'));
	$calcend = $calcend->format('Y-m-d H:i:s');
	$seatsql="SELECT DISTINCT seat_id
			 FROM seats
			 WHERE seat_id IN (SELECT seat_id							   	   				   			FROM seats								   				   	  		WHERE availability=TRUE)
				    AND seat_id NOT IN (SELECT seat_id													    FROM tests 					   					 				 WHERE scheduled_start_time BETWEEN '$startday' AND '$calcend')";
	$stmtseat = $pdo->query($seatsql);

	$seats = [];

	foreach($stmtseat as $row){
								
		array_push($seats,$row['seat_id']);

	}	


	if(count($seats)===0){echo "<script> alert('No Seat Available at the selected time'); </script>";

	}
	else{
 	$newseat = $seats[0];

	$sql = <<<_SQL_
		UPDATE tests
		SET   	student_id = ?,
			seat_id =?,
			instructor_id = ?,
			employeeID = ?,
			scheduled_start_time = ?,
			actual_start_time = ?,
			actual_end_time = ?,
			test_status = ?,
			test_type = ?,
			delivered = ?
		WHERE student_id = ? AND instructor_id = ? AND employeeID = ? AND scheduled_start_time = ?  AND seat_id = ?;	
_SQL_;
	
        $varActStart = null;
        $varActEnd = null;
	
	if (isset($_POST['actStart']) and $_POST['actStart']!='NULL')
	{	
			
		$t1 = date_create("8:00:00");
		$startT1 = $t1->format("H:i:s");

		$t2 = date_create("12:00:00");
		$endT1 = $t2->format("H:i:s");

		$t3 = date_create("13:00:00");
		$startT2 = $t3->format("H:i:s");
		
		$t4 = date_create("17:00:00");
		$endT2 = $t4->format("H:i:s");

	// if actStart is between 8:00 and 12:00
		if ($_POST['actStart']>= $startT1 and $_POST['actStart'] <= $endT1) 
		{	// if actEnd is between 8:00 and 12:00
			// or actEnd is not set yet
			if (($_POST['actEnd']>= $startT1 and $_POST['actEnd'] <= $endT1 and $_POST['actStart']<$_POST['actEnd']) or $_POST['actEnd']==='NULL' )
			{
				$varActStart = $_POST['actStart'];
				$varActEnd = $_POST['actEnd']; 
			}
		}
		// if actStart is between 1:00 and 4:00
		elseif ($_POST['actStart']>= $startT2 and $_POST['actStart'] <= $endT2) 
		{
			// if actEnd is between 1:00 and 4:00
			// or actEnd is not set yet
			if (($_POST['actEnd']>= $startT2 and $_POST['actEnd'] <= $endT2 and $_POST['actStart']<$_POST['actEnd']) or $_POST['actEnd'] ==='NULL')
			{
				$varActStart = $_POST['actStart'];
				$varActEnd = $_POST['actEnd']; 
			}
		}
		else
		{
			debug_message("invalid actual start or end time");
		}


	}

	if ($varActStart != null)
	{
		$varActStart = $_POST['pickday']." ".$_POST['actStart'].":00";
		$varActEnd = $_POST['pickday']." ".$_POST['actEnd'].":00";
		//$varActStart = new DateTime($varActStart);
		//$varActEnd = new DateTime($varActEnd);
	//	debug_message("add day to start: ".$varActStart);
	//	debug_message("add day to end: ".$varActEnd);
	}


	
	if (isset($_POST['actStart']))
	{
		$varTStat = "In Progress";
	}
	elseif (isset($_POST['actEnd']))
	{
		$varTStat = "Completed";
	}
	else
	{
		$varTStat = "Incomplete";
	}

		
	//$varDay = $_POST['pickday'];
	//$varD = $varDay->format('Y-M-D');
	$varTimeStart = $_POST['pickday']." ".$_POST['pickTime'];
	$sStartTime = new DateTime("$varTimeStart");
	//if (isset($_POST['delivered'] && $_POST['delivered']
	$stuPost = $_POST['stuid'];
	$proctorSQL = "SELECT employeeID AS emp
			FROM shifts
			WHERE '{$varTimeStart}' BETWEEN start_time and end_time;";
	$stmt2 = $pdo->query($proctorSQL);
	foreach($stmt2 as $row)
	{
		$emp = $row['emp'];
	}


	$stmt = $pdo->prepare($sql);
	try{
		$d2 = [$stuPost, $newseat, $_POST['insid'], $emp, $varTimeStart, $varActStart, $varActEnd, $varTStat, $_POST['type'], $_POST['delivered'], $stuPost, $_POST['insid'], $_POST['empid'], $_POST['stime'], $_POST['sid']];
		
		$stmt->execute($d2);
		$oldtime = $_POST['stime'];


	}
	catch (\PDOException $e)
	{
		if ($e->getCode() == 23505)
		{
			debug_message("Update failed. Error message: ".$e);
			throw new \PDOException($e->getMessage(), (int)$e->getCode());
		}
		else
		{
			throw new \PDOException($e->getMessage(), (int)$e->getCode());
		}

	}


	}
}

function deleteRecord($pdo)
{

	$sql = 'DELETE FROM tests WHERE seat_id = :seat_id
					AND student_id = :student_id 
					AND instructor_id = :instructor_id
					AND employeeID = :employeeID
					AND scheduled_start_time = :scheduled_start_time ';
	$stmt = $pdo->prepare($sql);
	

	try
	{
		$data = ['seat_id' 		=> 	$_POST['sid'],
			'student_id'		=>	$_POST['stuid'],
			'instructor_id'		=>	$_POST['insid'],
			'employeeID'		=>	$_POST['empid'],
			'scheduled_start_time'	=>	$_POST['stime'] ];

		$stmt->execute($data);
	}	
	catch (\PDOExecute $e)
	{
		debug_message("deletion failed. Error message: ".$e);
		throw new \PDOExpection($e->getMessage(), (int)$e->getCode());
	}
	
}


function process_post($pdo)
{
	if (isset($_POST['update']))
	{
		update($pdo);
	}
	elseif (isset($_POST['delete']))
	{
		deleteRecord($pdo);
	}

//	header($_SERVER['HTTP_REFERER']);

}

function main()
{
	head("edit form");
	
	echo "<h1>Edit a Student's Registration</h1>";
	$pdo = connect_to_psql('genesis','matthew_ko','123');
	if (isset($_POST['edit']))
	{
		$editID = $_POST['edit'];
		$idArray = explode(',', $editID);
		$stu_id = $idArray[0];
		$ins_id = $idArray[1];
		$emp_id = $idArray[2];
		$s_id = $idArray[3];
		$start_time = $idArray[4];
		buildForm($pdo, $stu_id, $ins_id, $emp_id, $s_id, $start_time);
		
	}
	
	$utype = $_SESSION['utype'];
	if($utype === 1){links("instructor_home.php","Home");}
	elseif($utype === 2){links("employee_home.php","Home");}
	process_post($pdo);
	foot(); 
}
main();
	
?>
