<?php session_start() ?>
<?php

/*Author: Matt Ko
*student_home.php
*/
require('function.php');

function home($pdo, $id){
    
    echo "<h1>Current Schedule</h1>";
    
    debug_message('student homepage function');
    $sql = "SELECT instructor_first_name || ' ' || instructor_last_name AS instructor, scheduled_start_time, test_type, test_status FROM instructors NATURAL JOIN tests WHERE scheduled_start_time > NOW() AND student_id = :id ORDER BY scheduled_start_time";
    $stmt = $pdo->prepare($sql);
    try{
        $data = array('id' => $id);
        $stmt->execute($data);
    }
    catch(\PDOException $e){
		debug_message('Failed query. Error message: ' . $e);
		throw new \PDOException($e->getMessage(), (int)$e->getCode());
	}
	
    $labels = ['Instructor','Start Time','Type','Status'];
    $cols = ['instructor','scheduled_start_time','test_type','test_status'];
    
    echo "\n<table>\n";
    echo "<tr>";
    
    foreach($labels as $th){
        echo '<th>'. $th . '</th>';
    }
    echo "</tr>\n";
    
    debug_message('Table started for student home');
    
    foreach($stmt as $row){
        echo "<tr>\n";
        
        foreach($cols as $col){
            $td = $row[$col];
            echo "<td>".$td."</td>\n";
        }
        echo "</tr>\n";
        
    }
    echo "</table>\n";
}

function main(){
    
    $pdo = connect_to_psql('genesis','matthew_ko',"123");
    $id = $_SESSION['user_id'];
    debug_message("id is $id");
    head("STUDENT HOME");
    //popshift($pdo);
    echo "<div class='home'>";
    home($pdo,$id);
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
		            links('student_home.php','Home');
		      echo '</li>';						          
		      echo '<li>';
			 logoutButton('emlogout');
		      echo '</li>';
		      echo '</ul>';
	           echo "</div>";

    foot();
    
}

main();
