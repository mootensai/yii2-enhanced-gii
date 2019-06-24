-- phpMyAdmin SQL Dump
-- version 4.7.9
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Aug 06, 2018 at 02:15 AM
-- Server version: 10.1.31-MariaDB
-- PHP Version: 7.2.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `inquid_enhanced_creator`
--

-- --------------------------------------------------------

--
-- Table structure for table `ccolumn`
--

CREATE TABLE `ccolumn` (
  `id` int(11) NOT NULL,
  `col_name` varchar(100) NOT NULL COMMENT 'Column Name',
  `uuid` tinyint(1) DEFAULT NULL COMMENT 'Does this column will be autoincremented?',
  `col_id` int(11) DEFAULT NULL COMMENT 'number of the column, must be unique.',
  `length` int(11) DEFAULT NULL,
  `require` tinyint(1) DEFAULT NULL COMMENT 'Cannot be null?',
  `display_text` varchar(200) DEFAULT NULL COMMENT 'The text to be displayed in the labels',
  `hint` varchar(100) DEFAULT NULL COMMENT 'The hint message to be dispayed',
  `label_pos` int(11) DEFAULT NULL COMMENT 'Label Position right, center or left.',
  `label_size` int(11) DEFAULT NULL COMMENT 'Size of the label',
  `primary_key` tinyint(1) DEFAULT NULL COMMENT 'The column is the primary Key?',
  `type` enum('int','string','boolean','date','float','long') DEFAULT NULL COMMENT 'Type of the column',
  `relation` varchar(100) DEFAULT NULL,
  `relation_type` int(11) DEFAULT NULL COMMENT 'One to one relation\nOne to Many\nMany to Many',
  `relation_field` varchar(100) DEFAULT NULL COMMENT 'The column name of the Foregin table to be used.\nThe connection will be seen as spinner or combobox and this column need to contain the values to show.',
  `default_values` text COMMENT 'Default values to be inserted in the databse.',
  `id_table` int(11) NOT NULL COMMENT 'Table',
  `input_widget` enum('textInput','textarea','password','email','file','checkBox','checkBoxGroup','radioButtonGroup','button','dropdown','datePicker','starRating','switchInput','rangeInput','spinner') DEFAULT NULL COMMENT 'Widget to be displayed',
  `display_widget` enum('default','image','video','map','whatsapp') DEFAULT NULL,
  `static` tinyint(1) DEFAULT NULL COMMENT 'Values will be saved in code like an Enum',
  `description` text COMMENT 'Description',
  `generated` tinyint(1) DEFAULT '0' COMMENT 'Does this column was already generated',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `custom`
--

CREATE TABLE `custom` (
  `id` int(11) NOT NULL,
  `name` varchar(45) DEFAULT NULL COMMENT 'Custom name to be used in the templates',
  `value` text,
  `id_project` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ddatabase`
--

CREATE TABLE `ddatabase` (
  `id` int(11) NOT NULL,
  `host` varchar(50) DEFAULT NULL COMMENT 'Ip or Domain',
  `database_name` varchar(50) DEFAULT NULL,
  `host_type` enum('sqlite','mysql','postgres','oracle','sqlserver') DEFAULT NULL COMMENT 'Database Engine',
  `username` varchar(45) DEFAULT NULL COMMENT 'Database User',
  `password` varchar(45) DEFAULT NULL COMMENT 'Database Password',
  `port` int(11) DEFAULT NULL,
  `project_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ddatabase`
--

INSERT INTO `ddatabase` (`id`, `host`, `database_name`, `host_type`, `username`, `password`, `port`, `project_id`, `created_at`, `updated_at`, `deleted_at`, `created_by`, `updated_by`, `deleted_by`) VALUES
(4, 'localhost', 'servisum', 'mysql', 'root', NULL, NULL, 4, NULL, NULL, NULL, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `design`
--

CREATE TABLE `design` (
  `id` int(11) NOT NULL,
  `theme` varchar(45) NOT NULL COMMENT 'Theme to customize the app.',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `design`
--

INSERT INTO `design` (`id`, `theme`, `created_at`, `updated_at`, `deleted_at`, `created_by`, `updated_by`, `deleted_by`) VALUES
(1, 'adminlte', NULL, NULL, NULL, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `extras`
--

CREATE TABLE `extras` (
  `id` int(11) NOT NULL,
  `uml_diagrams` tinyint(1) DEFAULT NULL COMMENT 'Generate UML diagrams?',
  `quoting` tinyint(1) DEFAULT NULL COMMENT 'Generate Bill for the cost of the generated app.',
  `document` tinyint(1) DEFAULT NULL COMMENT 'Document the project',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `extras`
--

INSERT INTO `extras` (`id`, `uml_diagrams`, `quoting`, `document`, `created_at`, `updated_at`, `deleted_at`, `created_by`, `updated_by`, `deleted_by`) VALUES
(1, 1, 1, 1, NULL, NULL, NULL, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `languages`
--

CREATE TABLE `languages` (
  `idLanguage` int(11) NOT NULL,
  `code` varchar(5) DEFAULT NULL COMMENT 'Code language max 3 digits, for example en -> english, es -> spanish',
  `name` varchar(45) DEFAULT NULL COMMENT 'Full name like Italian, Spanish, Russian',
  `id_project` int(11) NOT NULL COMMENT 'Project',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `module`
--

CREATE TABLE `module` (
  `id` int(11) NOT NULL,
  `name` varchar(45) DEFAULT NULL,
  `projects_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `module`
--

INSERT INTO `module` (`id`, `name`, `projects_id`, `created_at`, `updated_at`, `deleted_at`, `created_by`, `updated_by`, `deleted_by`) VALUES
(1, 'facturacion', 4, NULL, NULL, NULL, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `oauth_credentials`
--

CREATE TABLE `oauth_credentials` (
  `id` int(11) NOT NULL,
  `app` varchar(45) DEFAULT NULL COMMENT 'Name of the app (Facebook, twitter, Google, etc.)',
  `app_id` varchar(100) DEFAULT NULL COMMENT 'App Id',
  `secret` varchar(100) DEFAULT NULL COMMENT 'App Secret',
  `id_project` int(11) NOT NULL COMMENT 'Project',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` int(11) NOT NULL,
  `name` varchar(200) DEFAULT NULL COMMENT 'Name of the project',
  `description` text COMMENT 'Description',
  `userName` varchar(45) DEFAULT NULL,
  `date_format` varchar(45) DEFAULT 'Y-m-d' COMMENT 'Date and Time format (Default YYYY-MM-DD HH:MM:SS)',
  `force_update` tinyint(1) DEFAULT NULL COMMENT 'The system will overwrite the generated code when the system is executed.',
  `extras_id` int(11) DEFAULT NULL COMMENT 'Extra Options to add to the proyect.',
  `design_id` int(11) DEFAULT NULL COMMENT 'Change the theme of your app to customize it.',
  `repository_id` int(11) DEFAULT NULL COMMENT 'Connect with a repository to share, analyze changes and backup code.',
  `client_id` int(11) DEFAULT NULL COMMENT 'The data of your company to be used in the legal files and the billing.',
  `url` varchar(250) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `name`, `description`, `userName`, `date_format`, `force_update`, `extras_id`, `design_id`, `repository_id`, `client_id`, `url`, `created_at`, `updated_at`, `deleted_at`, `created_by`, `updated_by`, `deleted_by`) VALUES
(4, 'Servisum', 'Inquid ERP', NULL, 'Y-m-d', 1, NULL, 1, 2, 1, 'https://servisum.inquid.co', NULL, NULL, NULL, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `repository`
--

CREATE TABLE `repository` (
  `id` int(11) NOT NULL,
  `host` varchar(45) NOT NULL COMMENT 'Host, for default:\nhttps://github.com',
  `name` varchar(45) NOT NULL COMMENT 'Repository name',
  `user` varchar(45) NOT NULL COMMENT 'Username',
  `email` varchar(45) DEFAULT NULL COMMENT 'Email',
  `password` varchar(45) NOT NULL COMMENT 'Password',
  `token` varchar(200) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `repository`
--

INSERT INTO `repository` (`id`, `host`, `name`, `user`, `email`, `password`, `token`, `created_at`, `updated_at`, `deleted_at`, `created_by`, `updated_by`, `deleted_by`) VALUES
(2, 'https://github.com', 'inquid', 'gogl92', 'luisarmando1234@gmail.com', 'b53544a70d4e719d4692929d99bfd6391e446861', NULL, NULL, NULL, NULL, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `ttable`
--

CREATE TABLE `ttable` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL COMMENT 'Name of the table',
  `fields_pagination` int(11) DEFAULT NULL COMMENT 'Fields per screen',
  `display_id` tinyint(1) DEFAULT NULL COMMENT 'Display the Id of the table?',
  `quit_without_changes` tinyint(1) DEFAULT NULL COMMENT 'The user can leave the app without saving it?',
  `persistent` tinyint(1) DEFAULT NULL COMMENT 'Data will be stored in database even if it is not complete?',
  `deletion_cause` tinyint(1) DEFAULT NULL COMMENT 'Add causes to quit the full fill of the table, those wil be prompted when the user try to exit.',
  `before_save` text COMMENT 'Add a method name to be executed when the table is sent.',
  `aftersave` text,
  `visible_grid` tinyint(1) DEFAULT NULL COMMENT 'The data can be seen from a grid?',
  `generate_fake_data` tinyint(1) DEFAULT NULL COMMENT 'Generate fake data based in the columns name?',
  `document` tinyint(1) DEFAULT NULL COMMENT 'Document the table? Creates a markdown file with the things information created.',
  `type` enum('Primary','Secondary','Tertiary') DEFAULT NULL COMMENT 'The type of the table.\nPrimary -> the user can Create, Edit and Delete. (Octopuse)\nSecondary -> the table can be just used in a relation from another table like a table of colors to add the colors of the cars that the user fills. (Tentacle)\nTertiary -> a table that is the result from a many to many relation.',
  `primary_key` varchar(45) DEFAULT 'id' COMMENT 'The column taht will be the primary key.',
  `description` varchar(45) DEFAULT NULL COMMENT 'Description',
  `module_id` int(11) NOT NULL,
  `generated` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `user_module`
--

CREATE TABLE `user_module` (
  `id` int(11) NOT NULL COMMENT '			',
  `admin_name` varchar(45) DEFAULT 'admin',
  `password` varchar(45) DEFAULT '123456',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

CREATE TABLE `user_roles` (
  `id` int(11) NOT NULL,
  `name` varchar(45) NOT NULL,
  `level` varchar(45) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ccolumn`
--
ALTER TABLE `ccolumn`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `colId_UNIQUE` (`col_id`),
  ADD KEY `fk_Column_Table1_idx` (`id_table`);

--
-- Indexes for table `custom`
--
ALTER TABLE `custom`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_Custom_Projects1_idx` (`id_project`);

--
-- Indexes for table `ddatabase`
--
ALTER TABLE `ddatabase`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_ddatabase_projects1_idx` (`project_id`);

--
-- Indexes for table `design`
--
ALTER TABLE `design`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `extras`
--
ALTER TABLE `extras`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `languages`
--
ALTER TABLE `languages`
  ADD PRIMARY KEY (`idLanguage`),
  ADD KEY `fk_languages_Projects1_idx` (`id_project`);

--
-- Indexes for table `module`
--
ALTER TABLE `module`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_module_projects1_idx` (`projects_id`);

--
-- Indexes for table `oauth_credentials`
--
ALTER TABLE `oauth_credentials`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_OAuthCredentials_Projects1_idx` (`id_project`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_Projects_Extras1_idx` (`extras_id`),
  ADD KEY `fk_Projects_Design1_idx` (`design_id`),
  ADD KEY `fk_Projects_Repository1_idx` (`repository_id`);

--
-- Indexes for table `repository`
--
ALTER TABLE `repository`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ttable`
--
ALTER TABLE `ttable`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_ttable_module1_idx` (`module_id`);

--
-- Indexes for table `user_module`
--
ALTER TABLE `user_module`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ccolumn`
--
ALTER TABLE `ccolumn`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `custom`
--
ALTER TABLE `custom`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ddatabase`
--
ALTER TABLE `ddatabase`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `design`
--
ALTER TABLE `design`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `extras`
--
ALTER TABLE `extras`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `languages`
--
ALTER TABLE `languages`
  MODIFY `idLanguage` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `module`
--
ALTER TABLE `module`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `oauth_credentials`
--
ALTER TABLE `oauth_credentials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `repository`
--
ALTER TABLE `repository`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `ttable`
--
ALTER TABLE `ttable`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_module`
--
ALTER TABLE `user_module`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '			';

--
-- AUTO_INCREMENT for table `user_roles`
--
ALTER TABLE `user_roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `ccolumn`
--
ALTER TABLE `ccolumn`
  ADD CONSTRAINT `fk_Column_Table1` FOREIGN KEY (`id_table`) REFERENCES `ttable` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `custom`
--
ALTER TABLE `custom`
  ADD CONSTRAINT `fk_Custom_Projects1` FOREIGN KEY (`id_project`) REFERENCES `projects` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `ddatabase`
--
ALTER TABLE `ddatabase`
  ADD CONSTRAINT `fk_ddatabase_projects1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `languages`
--
ALTER TABLE `languages`
  ADD CONSTRAINT `fk_languages_Projects1` FOREIGN KEY (`id_project`) REFERENCES `projects` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `module`
--
ALTER TABLE `module`
  ADD CONSTRAINT `fk_module_projects1` FOREIGN KEY (`projects_id`) REFERENCES `projects` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `oauth_credentials`
--
ALTER TABLE `oauth_credentials`
  ADD CONSTRAINT `fk_OAuthCredentials_Projects1` FOREIGN KEY (`id_project`) REFERENCES `projects` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `projects`
--
ALTER TABLE `projects`
  ADD CONSTRAINT `fk_Projects_Design1` FOREIGN KEY (`design_id`) REFERENCES `design` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_Projects_Extras1` FOREIGN KEY (`extras_id`) REFERENCES `extras` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_Projects_Repository1` FOREIGN KEY (`repository_id`) REFERENCES `repository` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `ttable`
--
ALTER TABLE `ttable`
  ADD CONSTRAINT `fk_ttable_module1` FOREIGN KEY (`module_id`) REFERENCES `module` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
