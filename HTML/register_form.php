<!-- Creator: Courtney Gibson -->
<?php session_start() ?>
<?php
	require('function.php');

function buildForm($pdo)
{
    echo '<br />';
    
    /* select instructor */
    echo '<p> Select Instructor: </p>';

    echo "<form method='post'>";

    echo "<div class='outer'>";
    echo "<div class='dropdown'>";
    echo "<select name='instructor'>";
    $insid=$_SESSION['user_id'];
    $selectsql="SELECT instructor_first_name||' '|| instructor_last_name AS instructor_name
		  FROM instructors
		 WHERE instructor_id=$insid";
    $insname=$pdo->query($selectsql);
    while($row=$insname->fetch()){
     $name=$row['instructor_name'];
    }
    if($_SESSION['utype']==2){
	    DropDownInstructors($pdo);}
    else{
    echo "<option value='$insid'>$name</option>";
    }
    echo "</select>";
    echo "</div>";
    echo "</div>";

    /* select student */
    echo '<p> Select Student: </p>';
    echo "<div class='outer'>";
    echo "<div class='dropdown'>";
    echo "<select name='student'>";
    DropDownStudents($pdo);
    echo "</select>";
    echo "</div>";
    echo "</div>";

    /* test date */
    echo '<p> Select Test Date: </p>';
    echo "<div class='outer'>";
    echo "<div class='dropdown'>";
    $date=date('Y-m-d');
    echo '<input type="date" name="pickday" min="'.$date.'" value="'.$date.'">';
    $stmt = $pdo->query("SELECT DISTINCT date(start_time) AS day
	    			FROM shifts
				WHERE start_time > NOW()
				ORDER BY day ASC;");
	echo "</div>";
    echo "</div>";
    
    /* test start time */
	echo '<p>Select Test Start Time: </p>';
	echo "<div class='outer'>";
    echo "<div class='dropdown'>";
	echo "<select name='pickTime'>";

	if (isset($_POST['pickDay']))
	{
		$selectedDay = $_POST['pickDay'];

		$sql = "SELECT DATEPART(hh:mm, start_time) AS start_hm, DATEPART(hh:mm, end_time) AS end_hm
			FROM shifts
			WHERE start_time >= '{$selectedDay}' AND start_time < 
			(SELECT DATEADD('{$selectedDay}', -1, GETDATE()))
					ORDER BY start_time ASC;";
		$stmt = $pdo->query($sql);
	}
	
	//$selDate = $_POST['pickDay'];
	//date_format($selDate, 'Y-M-D');
	$timeList = [];
	$beginTime = date_create("08:00:00");

	for ($i = 0; $i<= 48; $i++)
	{
		date_add($beginTime, date_interval_create_from_date_string("10 minutes"));
		date_format($beginTime, 'H:i:s');
	//	$beginTime = $selDate.$beginTime;
		$newT = $beginTime->format('H:i:s');
		array_push($timeList, $newT);
		
		
		$bTime = $beginTime->format('H:i');
		echo "<p>".$bTime."</p>";
	}

	foreach($timeList as $time)
	{
		//$optionTime = strtotime($time);
		echo "<option>".$time."</option>";
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
    echo"\n<input type='submit' value='register' name='register'>";
	echo "</form>";
}

function goodtime($pdo){

	$sTimeStart = $_POST['pickday']." ".$_POST['pickTime'];

	$noon = $_POST['pickday']." 12:00:00";
	$five = $_POST['pickday']." 17:00:00";
	$one = $_POST['pickday']." 13:00:00";


	$startdate = new DateTime("$sTimeStart");
	$noon = new DateTime("$noon");

	if($startdate>=$noon && $startdate < $one){
		print "Invalid Time";
		return false;
	}
	else{


		if($startdate<$noon){	
			$diff = $startdate -> diff( $noon );
			//print_r($startdate);
			//print_r($noon);
			$end = $noon;
		}
		else{
			$diff = $startdate -> diff( $five );
			//print_r($startdate);
			//print_r($five);
			$end=$five;
		}

		$duration = $_POST['duration'];
		$calcend = $startdate;
		$calcend -> add(new DateInterval('PT'.$duration.'M'));

		//print $diff -> format("%H:%I:%S");
		//print_r($calcend);

		if($calcend<$end){
			return true;

		}
		else{
			return false;
			
		}	
	}

}

function register($pdo)
{
				
	$sql = 'INSERT INTO tests (seat_id, student_id, instructor_id, employeeID, scheduled_start_time, actual_start_time, actual_end_time, test_status, test_type, delivered)';
	$sql .= ' VALUES (?,?, ?, ?, ?,?, ?, ?,?, ?)';
	$stmt = $pdo->prepare($sql);

	$startday = $_POST['pickday']." ".$_POST['pickTime'];
	$startdate = new DateTime("$startday");

	$duration = $_POST['duration'];

	$calcend = $startdate;
	$calcend -> add(new DateInterval('PT'.$duration.'M'));
	$calcend = $calcend->format('Y-m-d H:i:s');
	$seatsql="SELECT DISTINCT seat_id
		    FROM seats
		   WHERE seat_id IN (SELECT seat_id
	   				   FROM seats
				   	  WHERE availability=TRUE)
		     AND seat_id NOT IN (SELECT seat_id
					   FROM tests 
					  WHERE scheduled_start_time BETWEEN '$startday' AND '$calcend')";

	$stmtseat = $pdo->query($seatsql);

	$seats = [];

	foreach($stmtseat as $row){
	
		array_push($seats,$row['seat_id']);

	}	

	if(count($seats)===0){echo "<script> alert('No Seat Available at the selected time'); </script>";
	
		
	
	
	
	}
	else{ $varSeat = $seats[0];


		$employeesql = <<<_SQL_

			SELECT DISTINCT employeeID AS employeeid
				FROM shifts
				WHERE start_time <= '$startday' AND end_time >= '$startday'
_SQL_;
		
	
		$stmtemp = $pdo->query($employeesql);
		
		foreach($stmtemp as $row){

			$varEmployee = $row['employeeid'];
		}
		
		$varActStart = null;
		$varActEnd = null;
		$varTestStat = "not started";
		$varDel = 'false';
		$sTimeStart = $_POST['pickday']." ".$_POST['pickTime'];
	
	

		try{
		 $today = date("Y-m-d h:i:s");
		 $now = new DateTime("$today");
		 $start = new DateTime($sTimeStart);
		 $interval = date_diff($now,$start);
		 $days=$interval->format("%d");
		 $hours = $interval->format("%h");

	        if(($_SESSION['utype']==1 and $days!=0) or $_SESSION['utype']==2){
		$stmt->execute([$varSeat, $_POST['student'], $_POST['instructor'], $varEmployee, $sTimeStart, $varActStart, $varActEnd, $varTestStat, $_POST['type'], $varDel]);}
		else{
		echo "<script> alert('Please call proctor to register within 24 hours!'); </script>";
		}
		

		}
		catch (\PDOException $e)
		{
			if ($e->getCode() == 23505)
			{
				throw new \PDOException($e->getMessage(), (int)$e->getCode());
			}
			else
			{
				throw new \PDOException($e->getMessage(), (int)$e->getCode());
			}
		}

	}

	
	
}

function process_post($pdo)
{
	
	if (isset($_POST['register']))
	{
		if(goodtime($pdo)){
			register($pdo);
		}
		else{
			print "Invalid Session";
		}
	}
}

function main()
{
	head("register form");
	
	echo "<h1>Register a Student for a Test</h1>";
	$pdo = connect_to_psql('genesis','matthew_ko','123');
	buildForm($pdo);
	$utype = $_SESSION['utype'];
	if($utype === 1){ links("instructor_home.php",'Home');}
	elseif($utype ===2){links("employee_home.php",'Home');}

	process_post($pdo);	
	foot(); 
}
main();
	
?>
