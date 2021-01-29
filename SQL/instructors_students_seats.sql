DROP TABLE IF EXISTS instructors;
DROP TABLE IF EXISTS students;
DROP TABLE IF EXISTS seats;



/* instructors table */
CREATE TABLE instructors (
    PRIMARY KEY (instructor_id),
    instructor_id       	SERIAL,
    instructor_email    	VARCHAR(100)   NOT NULL UNIQUE,
	instructor_password		VARCHAR(50)   NOT NULL,
	instructor_first_name	VARCHAR(50)   NOT NULL,
	instructor_last_name	VARCHAR(50)   NOT NULL,
	instructor_is_active    BOOLEAN       NOT NULL
);

/* sample instructor data */
INSERT INTO instructors (instructor_id, instructor_email, instructor_password, instructor_first_name, instructor_last_name, instructor_is_active) -- sample data
VALUES 	(1, 'thomas.allen@centre.edu', 'password', 'Thomas', 'Allen', TRUE),
		(2, 'michael.bradshaw@centre.edu', 'csc', 'Michael', 'Bradshaw', TRUE),
		(3, 'david.toth@centre.edu', 'password2', 'David', 'Toth', TRUE),
		(4, 'christine.shannon@centre.edu', 'password3', 'Christine', 'Shannon', TRUE),
		(5, 'alex.mcallister@centre.edu', 'password4', 'Alex', 'McAllister', TRUE);
		
		
/*DELETE FROM instructors
    WHERE instructor_id = 5;

UPDATE instructors
SET instructor_password = "newPassword"
WHERE instructor_id = 4;*/
	

/* students table */
CREATE TABLE students (
    PRIMARY KEY (student_id),
    student_id       	SERIAL,
    student_email    	VARCHAR(100)   NOT NULL UNIQUE,
	student_password	VARCHAR(50)   NOT NULL,
	student_first_name	VARCHAR(50)   NOT NULL,
	student_last_name	VARCHAR(50)   NOT NULL,
	student_is_active   BOOLEAN        NOT NULL
);


/* sample student data */
INSERT INTO students (student_id, student_email, student_password, student_first_name, student_last_name, student_is_active) -- sample data
VALUES 	(1, 'courtney.gibson@centre.edu', 'agoodpassword', 'Courtney', 'Gibson', TRUE),
		(2, 'matthew.ko@centre.edu', 'password5', 'Matthew', 'Ko', TRUE),
		(3, 'xinping.huang@centre.edu', 'password6', 'Xinping', 'Huang', TRUE),
		(4, 'joe.smith@centre.edu', 'password7', 'Joe', 'Smith', TRUE),
		(5, 'Carrie.anderson@centre.edu', 'password8', 'Carrie', 'Anderson', TRUE);


/*DELETE FROM students
    WHERE student_id = 4;

UPDATE students
SET students_password = "newPassword"
WHERE student_id = 5;*/



/* seats table */
CREATE TABLE seats (
    PRIMARY KEY (seat_id),
    seat_id       SERIAL,
    has_computer	BOOLEAN	  	NOT NULL,
	availability	BOOLEAN		NOT NULL
);


/* sample seat data */
INSERT INTO seats (seat_id, has_computer, availability) -- sample data
VALUES 	(1, TRUE, FALSE),
		(2, TRUE, TRUE),
		(3, TRUE, FALSE),
		(4, TRUE, TRUE),
		(5, FALSE, TRUE),
		(6, FALSE, FALSE),
		(7, FALSE, TRUE),
		(8, FALSE, TRUE),
		(9, FALSE, TRUE),
		(10, FALSE, TRUE);
		
		
/*DELETE FROM seats
    WHERE seat_id = 4;

UPDATE seats
SET availability = FALSE
WHERE seat_id = 5;*/
		
		
		
		
		
		
		
		