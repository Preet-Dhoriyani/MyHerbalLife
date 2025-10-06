-- phpMyAdmin SQL Dump
-- Database: myherbal_db  (ya apne db ka naam yahan likh le)

CREATE TABLE IF NOT EXISTS `students` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `rollno` varchar(50) NOT NULL,
  `class` varchar(50) DEFAULT NULL,
  `marks` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sample data
INSERT INTO `students` (`name`, `rollno`, `class`, `marks`) VALUES
('Preet Dhoriyani', '101', '12th Sci', 89),
('Rahul Mehta', '102', '12th Com', 76),
('Kavya Patel', '103', '11th Arts', 91);
