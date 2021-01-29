DROP RULE IF EXISTS student_status ON students;
DROP RULE IF EXISTS instructor_status ON instructors;
DROP RULE IF EXISTS employee_status ON employees;
DROP RULE IF EXISTS seat_status ON seats;
DROP TABLE IF EXISTS tests;
DROP TABLE if EXISTS shifts CASCADE;
DROP TABLE IF EXISTS employees;
DROP TABLE IF EXISTS seats;
DROP TABLE IF EXISTS students;
DROP TABLE IF EXISTS instructors;
DROP TABLE IF EXISTS logins;

CREATE TABLE logins (
    PRIMARY KEY (username),
    username                VARCHAR(50) NOT NULL UNIQUE,
    passhash                VARCHAR(200) NOT NULL,
    utype                   INT         
);

INSERT INTO logins(username, passhash, utype)
VALUES  ('jsmith','$2y$10$Upxi5bZzeK.cUA9XfX6rVO7Q/FifqvbLd0h7hzhRlSWGdtI8xq1Ii',0),
        ('cgibson','$2y$10$FD.5/rMm4K8.Ss/X3/og1em8cI9ceD7nNLZacPFWykx5LlljpU852',0),
        ('mko','$2y$10$8BIjmiuOzBCKGhEu9XbDWuuprZFvKjm9yC579wLI2gPvuVrkzb3Mi',0),
        ('xhuang','$2y$10$aVgu0FzPzQojVqJHT78ALepSq8p.IHoQ1r9kDgnDBew1TpuKJW2VK',0),
        ('canderson','$2y$10$IKK0uHqX2rnPGH9/qSzg6.LThg2/y9/d51sLIc0fPQm63SVwOfvea',0),
        ('tallen','$2y$10$YJhF7PuhOS.4pSbOP4.5MOtpBntTBnQ.R9ByLxhnYvxtE6AS./Qu.',1),
        ('mbradshaw','$2y$10$gAUgjZl05PpFtPvciscCAewkV6mlEZIOEA8Gw.zrrrfigzgx4iovi',1),
        ('dtoth','$2y$10$hDGugSwEU.w.0ziYaN33xe0cBiHDdon.J4sK3aZhAaAkkEgRqc0UW',1),
        ('cshannon','$2y$10$zM.T5IfegfwlXBy7soPrmOuZx7bTAK3iuQGOb17rOKzgzh7uUDMxy',1),
        ('amcallister','$2y$10$fd/47IFgOlhb.9Z5QYr4fOIWIp9hrPBrkSx9SyqETw/6U5sk02zR.',1),
        ('mjohnson','$2y$10$9ufDQQvAoSTZKu7JwNMog.NEeEcJGd1hfDbH.ogCExphkxciUryNS',2),
        ('kcole','$2y$10$XAqC3uQ7bhGLz/Xg6vrO6uq9JKjognC1m3ieDeS2upYTZetxExAXK',2),
        ('theet','$2y$10$7m2eSf85osUKyJhL6FIj7eYgoTKJPtpOsaCh2Ixfj06KN4.38MJqu',2),
        ('aolimpia','$2y$10$lPm89wPNsYGADHpvyAxvi.m/AbeZfZJ0zvg5OE.XZzJ1yR7jGy2uO',2);


/* instructors table */
CREATE TABLE instructors (
    PRIMARY KEY (instructor_id),
    instructor_id       	SERIAL,
    instructor_email    	VARCHAR(100)   NOT NULL UNIQUE,
	instructor_password		VARCHAR(50)   NOT NULL,
	instructor_first_name	VARCHAR(50)   NOT NULL,
	instructor_last_name	VARCHAR(50)   NOT NULL,
	instructor_is_active    BOOLEAN       NOT NULL,
	username                VARCHAR(50)   NOT NULL REFERENCES logins (username) ON DELETE RESTRICT
);

/* sample instructor data */
INSERT INTO instructors (instructor_id, instructor_email, instructor_password, instructor_first_name, instructor_last_name, instructor_is_active, username) -- sample data
VALUES 	(1, 'thomas.allen@centre.edu', 'password', 'Thomas', 'Allen', TRUE, 'tallen'),
		(2, 'michael.bradshaw@centre.edu', 'csc', 'Michael', 'Bradshaw', TRUE, 'mbradshaw'),
		(3, 'david.toth@centre.edu', 'password2', 'David', 'Toth', TRUE, 'dtoth'),
		(4, 'christine.shannon@centre.edu', 'password3', 'Christine', 'Shannon', TRUE, 'cshannon'),
		(5, 'alex.mcallister@centre.edu', 'password4', 'Alex', 'McAllister', TRUE, 'amcallister');
		
		
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
	student_is_active   BOOLEAN        NOT NULL,
	username            VARCHAR(50)   NOT NULL REFERENCES logins (username) ON DELETE RESTRICT
);


/* sample student data */
INSERT INTO students (student_id, student_email, student_password, student_first_name, student_last_name, student_is_active, username) -- sample data
VALUES 	(1, 'courtney.gibson@centre.edu', 'agoodpassword', 'Courtney', 'Gibson', TRUE, 'cgibson'),
		(2, 'matthew.ko@centre.edu', 'password5', 'Matthew', 'Ko', TRUE, 'mko'),
		(3, 'xinping.huang@centre.edu', 'password6', 'Xinping', 'Huang', TRUE, 'xhuang'),
		(4, 'joe.smith@centre.edu', 'password7', 'Joe', 'Smith', TRUE, 'jsmith'),
		(5, 'Carrie.anderson@centre.edu', 'password8', 'Carrie', 'Anderson', TRUE, 'canderson');


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




/*CREATE TABLE for Employees */

CREATE TABLE employees (
	PRIMARY KEY (employeeID),
	employeeID		SERIAL, 
	employee_fname		VARCHAR(50) NOT NULL,		/*employee first name*/
	employee_lname		VARCHAR(50) NOT NULL,		/*employee last name*/
	employee_email		VARCHAR(100) NOT NULL UNIQUE,
	employee_password	VARCHAR(100) NOT NULL,
	employee_isProctor	BOOLEAN DEFAULT FALSE,
	employee_status     BOOLEAN NOT NULL DEFAULT TRUE,
	username            VARCHAR(50)   NOT NULL REFERENCES logins (username) ON DELETE RESTRICT
/*	employee_mobile_phone	VARCHAR(20) NOT NULL,
	employee_home_phone	VARCHAR(20) DEFAULT NULL, 
	employee_streetaddress	VARCHAR(40) NOT NULL,
	employee_state		CHAR(2) NOT NULL,
	employee_city		VARCHAR(20) NOT NULL,
	employee_zip		CHAR(5) */ 
);

/*CREATE shifts Table */
/* NOTE: Because the shifts are date time, we will keep the year the same (2019) for all shifts and focus on dates*/

CREATE TABLE shifts (
	PRIMARY KEY (shiftID),
	shiftID 		SERIAL,
	start_time		TIMESTAMP NOT NULL,
	end_time		TIMESTAMP NOT NULL, 
	employeeID		INT 	 
				REFERENCES employees (employeeID)
				ON DELETE RESTRICT
);

INSERT INTO employees ( employee_fname, employee_lname, employee_email, employee_password, employee_isProctor, username)
VALUES ('Martin',	'Johnson', 'mjohnson@acme.net', '123', TRUE, 'mjohnson'),
       ('Kim',		'Cole',	   'kcole@acme.net', '123', TRUE, 'kcole'),
       ('Terry',	'Heet', 'theet@acme.net', '123', TRUE, 'theet'),
       ('Alex',		'Olimpia', 'aolimpia@acme.net', '123', TRUE, 'aolimpia');



INSERT INTO shifts (start_time, end_time, employeeID)
VALUES
('2019-11-11 08:00:00', '2019-11-11 09:29:59', 1),
('2019-11-11 09:30:00', '2019-11-11 12:00:00', 2),
('2019-11-11 13:00:00', '2019-11-11 14:59:59', 3),
('2019-11-11 15:00:00', '2019-11-11 17:00:00', 4);

/*
CREATE VIEW shiftsource
AS
SELECT * 
	FROM shifts;

DECLARE
   counter INTEGER := 0 ; 
   i INTEGER := 0 ; 
   j INTEGER := 365 ;
BEGIN
    WHILE counter <= j LOOP
        counter := counter + 1 ; 
        INSERT INTO shifts (start_time, end_time, employeeID)
        SELECT start_time + (counter * INTERVAL '7 day'), end_time + (counter * INTERVAL '7 day'), employeeID
	    FROM shiftsource
    END LOOP ;
END;*/

/*
DELETE 
FROM employees
WHERE employeeID = 1;

UPDATE employees
SET employee_fname = 'Darryl'
WHERE employee_ID = 1;

DELETE
FROM shifts
WHERE start_time = '2019-01-01 08:00:00';

UPDATE shifts
SET start_time = '2019-01-01 07:30:00'
WHERE start_time = '2019-01-01 08:00:00'
*/
/* Create tests table */
CREATE TABLE tests(
    PRIMARY KEY (seat_id, student_id, instructor_id, employeeID, scheduled_start_time),
    seat_id               INT NOT NULL REFERENCES seats (seat_id) ON DELETE RESTRICT, 
    student_id            INT NOT NULL REFERENCES students (student_id) ON DELETE RESTRICT,
    instructor_id         INT NOT NULL REFERENCES instructors (instructor_id) ON DELETE RESTRICT,
    employeeID            INT NOT NULL REFERENCES employees (employeeID) ON DELETE RESTRICT,
    scheduled_start_time  TIMESTAMP NOT NULL,
    actual_start_time     TIMESTAMP,
    actual_end_time       TIMESTAMP,
    test_status           VARCHAR(50) NOT NULL,
    test_type             VARCHAR(50),
    delivered             BOOLEAN NOT NULL DEFAULT FALSE,
    CONSTRAINT valid_end_time CHECK (actual_end_time > actual_start_time),
    CONSTRAINT valid_start_time CHECK (actual_start_time >= scheduled_start_time)
);

/* create deny rules */
CREATE RULE seat_status AS
    ON DELETE TO seats DO INSTEAD
    UPDATE seats
    SET  availability = FALSE
    WHERE seat_id = OLD.seat_id;

CREATE RULE employee_status AS
    ON DELETE TO employees DO INSTEAD
    UPDATE employees
    SET employee_isProctor = FALSE
    WHERE employeeID = OLD.employeeID;

CREATE RULE instructor_status AS
    ON DELETE TO instructors DO INSTEAD
    UPDATE instructors
    SET instructor_is_active = FALSE
    WHERE instructor_id = OLD.instructor_id;

CREATE RULE student_status AS
    ON DELETE TO students DO INSTEAD
    UPDATE students
    SET student_is_active = FALSE
    WHERE student_id = OLD.student_id;

INSERT INTO tests(seat_id, student_id, instructor_id, employeeID, scheduled_start_time, actual_start_time, actual_end_time, test_status, test_type, delivered)
VALUES(1, 1, 2, 1, '2019-11-11 11:00:00', '2019-11-11 11:00:00', '2019-11-11 12:00:00', 'Complete', 'Online', TRUE),
      (2, 2, 4, 1, '2019-11-10 11:00:00', '2019-11-10 11:10:00', '2019-11-10 12:00:00', 'Complete', 'Paper', TRUE),
      (3, 3, 3, 3, '2019-12-11 11:00:00', NULL, NULL, 'Incomplete', 'Paper', False);
      
/*      
DELETE FROM shifts WHERE extract(isodow FROM start_time) = 6;
DELETE FROM shifts WHERE extract(isodow FROM start_time) = 7;

DELETE
FROM tests
WHERE seat_id=2
  AND student_id = 3
  AND instructor_id=4
  AND employeeID =1
  AND scheduled_start_time = '2019-11-10 11:00:00';

UPDATE tests
SET delivered = TRUE
WHERE seat_id=3
  AND student_id = 3
  AND instructor_id=3
  AND employeeID =3
  AND scheduled_start_time = '2019-12-11 11:00:00';
*/

CREATE RULE check_seat 
AS ON INSERT TO tests
WHERE seat_id IN (SELECT DISTINCT seat_id 
                     FROM seats 
                    WHERE availability != TRUE)
DO INSTEAD NOTHING;
