<?php 
session_start();


?>
<?php

require('function.php');

function home($pdo, $ins_id)
{
	echo "<h1>Current Schedule</h1>";
	debug_message('make instructor homepage');

	$sql = "SELECT student_first_name || student_last_name AS student, scheduled_start_time, actual_start_time, actual_end_time, test_type, test_status FROM students NATURAL JOIN tests WHERE scheduled_start_time > NOW() AND instructor_id = :id;";

	try{
	$stmt = $pdo->prepare($sql);
	

	$data = array('id' =>$ins_id);
	$stmt->execute($data);
		
	}
	catch(\PDOException $e){
		debug_message("failed query. Error message: ".$e);
		throw new \PDOException($e->getMessage(), (int)$e->getCode());

	}

	$headers = ["Student", "Scheduled Start Time","Actual Start Time", "Time Completed", "Type", "Status"];
	$cols = ['student', 'scheduled_start_time','actual_start_time','actual_end_time', 'test_type', 'test_status'];

	echo "\n<table>\n";
	echo "<tr>";

	foreach($headers as $th)
	{
		echo "<th>".$th."</th>";

	}

	echo "</tr>\n";


	debug_message("start table");

	
	

	foreach($stmt as $row)
	{
		echo "<tr>\n";
		foreach($cols as $col)
		{
			//debug_message($row[$col]);
			$td = $row[$col];
			echo "<td>".$td."</td>\n";
		}	
		echo "</tr>\n";
	}
	echo "</table>\n";
}	

function main()
{
	$pdo = connect_to_psql('genesis', 'matthew_ko', "123");
	$insid = $_SESSION['user_id'];
	head("Instructor home");
	//	home($pdo, $insid);
	echo "<div class='home'>";
		
	CurrentInsSchedule($pdo, $insid);
	echo "</div>";
	echo "<div class='menudiv'>";
		echo '<nav class="menu" tabindex="0">';
		echo "<ul>";
			echo '<li class="nohover">';
			echo '<img src="unknown-user.png" alt="No Image Upload">';
			echo '</li>';
			echo '<li class="nohover username">';
			      username();
			      echo '</li>';

				echo '<li>';
					links('instructor_home.php', 'Home');
				echo '</li>';
				echo '<li>';
					links('register_form.php', 'Register');
				echo '</li>';
				echo '<li>';
					logoutButton('emlogout');
				echo '</li>';
				echo '</ul>';
			echo "</div>";

	foot();

}
main();
