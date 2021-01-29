/* Matt Ko Project Initial SQL */

DROP TABLE if EXISTS shifts;
DROP TABLE IF EXISTS employees;

/*CREATE TABLE for Employees */

CREATE TABLE employees (
	PRIMARY KEY (employeeID),
	employeeID		SERIAL, 
	employee_fname		VARCHAR(50) NOT NULL,		/*employee first name*/
	employee_lname		VARCHAR(50) NOT NULL,		/*employee last name*/
	employee_email		VARCHAR(100) NOT NULL,
	employee_password	VARCHAR(100) NOT NULL,
	employee_isProctor	BOOLEAN DEFAULT FALSE,
	employee_active     BOOLEAN NOT NULL DEFAULT TRUE
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

INSERT INTO employees ( employee_fname, employee_lname, employee_email, employee_password, employee_isProctor)
VALUES ('Martin',	'Johnson', 'mjohnson@acme.net', '123', TRUE),
       ('Kim',		'Cole',	   'kcole@acme.net', '123', TRUE),
       ('Terry',	'Heet', 'theet@acme.net', '123', TRUE),
       ('Alex',		'Olimpia', 'aolimia@acme.net', '123', TRUE);



INSERT INTO shifts (start_time, end_time, employeeID)
VALUES
('2019-11-11 08:00:00', '2019-11-11 09:29:59', 1),
('2019-11-11 09:30:00', '2019-11-11 12:00:00', 2),
('2019-11-11 13:00:00', '2019-11-11 14:59:59', 3),
('2019-11-11 15:00:00', '2019-11-11 17:00:00', 4);

CREATE VIEW shiftsource
AS
SELECT * 
	FROM shifts;

DECLARE @i int = 1
WHILE @i < 365 
BEGIN
    SET @i = @i + 1
    
INSERT INTO shifts (start_time, end_time, employeeID)
SELECT start_time + (@i * INTERVAL '7 day'), end_time + (@i * INTERVAL '7 day'), employeeID
	FROM shiftsource
END;

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





						
	

