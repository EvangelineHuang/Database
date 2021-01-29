<?php session_start(); ?>
<?php

require('function.php');



function shifts($pdo, $id){


	echo "<div class='header'>";
	echo "<h1> Weekly Shifts  </h1>";
	echo "</div>";

	echo "<svg></svg>";


	$sql = <<<_SQL_

	SELECT start_time, end_time, extract(isodow from start_time) AS day
	FROM shifts
	WHERE start_time < (NOW() + INTERVAL '7 DAY') AND start_time > NOW()
		AND employeeid = :id

_SQL_;

	
	$stmt = $pdo->prepare($sql);
	try{
		$data = array('id' => $id);
		$stmt->execute($data);
	}
	catch(\PDOException $e){
		debug_message('Failed query. Error message: ' . $e);
		throw new \PDOException($e->getMessage(), (int)$e->getCode());
	}

	$labels = ['Start Time','End Time','Day of Week'];
	$cols   = ['start_time','end_time'];

	echo "\n<div class = 'shifttable'>\n";
	echo "<table>";
	echo "<tr>";

	foreach($labels as $th){
		echo '<th>'. $th . '</th>';
	}
	echo "</tr>\n";


	foreach($stmt as $row){

		echo "<tr>\n";
		echo "<td class='start'>".$row['start_time']."</td>";
		echo "<td class='end'>".$row['end_time']."</td>";
		echo "<td class='dow'>".$row['day']."</td>";
		echo "</tr>\n";
	
	}

	echo "</table>\n";
	echo "</div>";



}

function main(){


	$pdo = connect_to_psql('genesis','matthew_ko','123');
	$id = $_SESSION['user_id'];
	head("Shifts");
	echo "<div class='home'>";
		shifts($pdo,$id);
	echo "</div>";


	echo "<div class='menudiv'>";
      echo '<nav class="menu" tabindex="0">';
      echo '<ul>';
      echo '<li class="nohover">';
      echo '<img src="unknown-user.png" alt="No Image Upload">';
      echo '</li>';
      echo '<li class="nohover username">';
      
	      username();
    
      echo '</li>';
      echo '<li>';
      links('employee_home.php','Home');
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
      echo "</div>";
	
	$html = <<<_HTML_
	</body>
	<script type="text/javascript" src="shifts.js"></script>
	</html>	

_HTML_;

	echo $html;
}

main();


?>
