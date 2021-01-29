<?php session_start(); ?>

<?php

require('function.php');

$pdo = connect_to_psql('genesis','matthew_ko',"123" ,$verbose=TRUE);

if(isset($_POST['login'])){



$username = $_POST['username'];
$password = $_POST['password'];
$username = "$username";
$password = "$password";

$sql = "SELECT * FROM logins WHERE username = :u";
$stmt = $pdo->prepare($sql);
try{
        $data = ['u' => $username];
	$stmt->execute($data);
	debug_message('success query 1');
}
catch(\PDOException $e){
	debug_message('Failed query. Error message: ' . $e);	
	throw new \PDOException($e->getMessage(), (int)$e->getCode());
	header("Location: login.php");
	echo "<h5> Invalid Username or Password </h5>";

}

foreach($stmt as $row){

	$utype = $row['utype'];
	$hash = $row['passhash'];

	if($row['utype']===0){
		
	$sql2 = "SELECT student_id AS id, username FROM students NATURAL JOIN logins WHERE username = :u2";

	}
	elseif($row['utype']===1){

	$sql2 = "SELECT instructor_id AS id, username FROM instructors NATURAL JOIN logins WHERE username = :u2";

	}
	elseif($row['utype']===2){
	
	$sql2 = "SELECT employeeid AS id, username FROM employees NATURAL JOIN logins WHERE username = :u2";

	}
	else{
		echo "<h5> Invalid Username or Password </h5>";
	}
}
	
	try
	{
		$stmt2 = $pdo->prepare($sql2);
		$data = [ 'u2' => $username ];
		$stmt2->execute($data);
		debug_message('query successful');
	}
	catch (\PDOException $e)
	{
		//debug_message('DB Query Failed. Error: ' . $e);
		//throw new \PDOException($e->getMessage(), (int)$e->getCode());
		header("Location: logins.php");
		echo "<h5> Invalid Username or Password </h5>";

		
	}


	foreach($stmt2 as $row){


		$userid = $row['id'];

	}

	//$passhash = password_hash($password, PASSWORD_DEFAULT);



	if(password_verify($password, $hash)){

		$_SESSION['user_id'] = $userid;

		if($utype === 0){ header("Location: student_home.php");}
		elseif($utype === 1){ header("Location: instructor_home.php");}
		else{ header("Location: employee_home.php");}

	}else{
		echo "<h5> Invalid Username or Password </h5>";
		//header("Location: logins.php");
	
	}

}

//UPDATE logins SET passhash = '$2y$10$kAe3ENL1G664oThb3ZBjreaaxLv161kSSleqi7u.V1yXDA8m9OBiS' WHERE username = 'mko';


head('LOGIN');
$html = <<<_HTML_
<form action="" method="post">
    <input type="text" name="username" placeholder="Enter your username" required>
    <input type="password" name="password" placeholder="Enter your password" required>
    <input type="submit" name = "login" value="Submit">
</form>

_HTML_;

echo $html;
foot()

?>
