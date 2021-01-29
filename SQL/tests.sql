DROP TABLE IF EXISTS tests;

CREATE TABLE tests(
    PRIMARY KEY (seat_id, student_id, instructor_id, employee_id, scheduled_start_time),
    seat_id               INT NOT NULL REFERENCES seats (seat_id) ON DELETE RESTRICT, 
    student_id            INT NOT NULL REFERENCES students (student_id) ON DELETE RESTRICT,
    instructor_id         INT NOT NULL REFERENCES instructors (instructor_id) ON DELETE RESTRICT,
    employee_id           INT NOT NULL REFERENCES employees (employee_id) ON DELETE RESTRICT,
    scheduled_start_time  TIMESTAMP NOT NULL,
    actual_start_time     TIMESTAMP,
    actual_end_time       TIMESTAMP,
    test_status           VARCHAR(50) NOT NULL,
    test_type             VARCHAR(50),
    delivered             BOOLEAN NOT NULL DEFAULT FALSE,
    CONSTRAINT valid_end_time CHECK (actual_end_time > actual_start_time),
    CONSTRAINT valid_start_time CHECK (actual_start_time >= scheduled_start_time)
);

CREATE RULE seat_status AS
    ON DELETE TO seats DO INSTEAD
    UPDATE seats
    SET  availability = FALSE
    WHERE seat_id = OLD.seat_id;

CREATE RULE employee_status AS
    ON DELETE TO employees DO INSTEAD
    UPDATE employees
    SET employee_isProctor = FALSE
    WHERE employee_id = OLD.employee_id;

CREATE RULE instructor_status AS
    ON DELETE TO instructors DO INSTEAD
    UPDATE instructors
    SET instructor_is_active = FALSE
    WHERE intructor_id = OLD.instructor_id;

CREATE RULE student_status AS
    ON DELETE TO students DO INSTEAD
    UPDATE students
    SET student_is_active = FALSE
    WHERE student_id = OLD.student_id;

INSERT INTO tests(seat_id, student_id, instructor_id, employee_id, scheduled_start_time, actual_start_time, actual_end_time, test_status, test_type, delivered)
VALUES(1, 1, 2, 1, 2019-11-11 11:00:00, 2019-11-11 11:00:00, 2019-11-11 12:00:00, 'Complete', 'Online', TRUE),
      (2, 2, 4, 1, 2019-11-10 11:00:00, 2019-11-10 11:10:00, 2019-11-10 12:00:00,),'Complete', 'Paper', TRUE),
      (3, 3, 3, 3, 2019-12-11 11:00:00, NULL, NULL, 'Incomplete', 'Paper', False);
