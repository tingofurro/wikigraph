-- phpMyAdmin SQL Dump
-- version 3.5.2.2
-- http://www.phpmyadmin.net
--
-- Client: 127.0.0.1
-- Généré le: Jeu 08 Janvier 2015 à 20:36
-- Version du serveur: 5.5.27-log
-- Version de PHP: 5.4.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `wikigraph`
--

-- --------------------------------------------------------

--
-- Structure de la table `wg_category`
--

CREATE TABLE IF NOT EXISTS `wg_category` (
  `id` int(100) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `parent` int(100) NOT NULL,
  `distance` int(10) NOT NULL,
  `killBranch` int(10) NOT NULL,
  `travelled` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=156 ;

--
-- Contenu de la table `wg_category`
--

INSERT INTO `wg_category` (`id`, `name`, `parent`, `distance`, `killBranch`, `travelled`) VALUES
(1, 'Mathematics', 0, 0, 0, 1),
(3, 'Mathematical_examples', 1, 1, 0, 1),
(4, 'Applied_mathematics', 1, 1, 0, 1),
(5, 'Mathematics_and_culture', 1, 1, 0, 1),
(6, 'Elementary_mathematics', 1, 1, 0, 1),
(7, 'Fields_of_mathematics', 1, 1, 0, 1),
(8, 'History_of_mathematics', 1, 1, 0, 1),
(10, 'Mathematical_concepts', 1, 1, 0, 1),
(12, 'Mathematical_notation', 1, 1, 0, 1),
(13, 'Philosophy_of_mathematics', 1, 1, 0, 1),
(14, 'Mathematical_problem_solving', 1, 1, 0, 1),
(15, 'Pseudomathematics', 1, 1, 0, 1),
(16, 'Mathematical_terminology', 1, 1, 0, 1),
(17, 'Mathematical_tools', 1, 1, 0, 1),
(18, 'Mathematics_stubs', 1, 1, 0, 1),
(19, 'Actuarial_science', 4, 2, 0, 0),
(20, 'Computational_mathematics', 4, 2, 0, 0),
(21, 'Computational_science', 4, 2, 0, 0),
(22, 'Cryptography', 4, 2, 0, 0),
(23, 'Cybernetics', 4, 2, 0, 0),
(24, 'Mathematical_economics', 4, 2, 0, 0),
(25, 'Mathematical_finance', 4, 2, 0, 0),
(26, 'Mathematical_and_theoretical_biology', 4, 2, 0, 0),
(27, 'Mathematical_chemistry', 4, 2, 0, 0),
(28, 'Mathematical_physics', 4, 2, 0, 0),
(29, 'Mathematical_psychology', 4, 2, 0, 0),
(30, 'Mathematics_in_medicine', 4, 2, 0, 0),
(31, 'Mathematics_of_music', 4, 2, 0, 0),
(32, 'Operations_research', 4, 2, 0, 0),
(33, 'Probability_theory', 4, 2, 0, 0),
(34, 'Signal_processing', 4, 2, 0, 0),
(35, 'Theoretical_computer_science', 4, 2, 0, 0),
(36, 'Applied_mathematics_stubs', 4, 2, 0, 0),
(37, 'Mathematics_by_culture', 5, 2, 0, 0),
(38, 'Mathematics_awards', 5, 2, 0, 0),
(39, 'Mathematics_fiction_books', 5, 2, 0, 0),
(40, 'Mathematics_competitions', 5, 2, 0, 0),
(41, 'Mathematics_conferences', 5, 2, 0, 0),
(42, 'Documentary_television_series_about_mathematics', 5, 2, 0, 0),
(43, 'Mathematics_education', 5, 2, 0, 0),
(44, 'M._C._Escher', 5, 2, 0, 0),
(45, 'Ethnomathematicians', 5, 2, 0, 0),
(46, 'Fermat%27s_Last_Theorem', 5, 2, 0, 0),
(47, 'Films_about_mathematics', 5, 2, 0, 0),
(48, 'Mathematical_humor', 5, 2, 0, 0),
(49, 'Mathematics_and_art', 5, 2, 0, 0),
(50, 'Mathematics_and_mysticism', 5, 2, 0, 0),
(51, 'Mathematics_of_music', 5, 2, 0, 0),
(52, 'Mathematics-related_topics_in_popular_culture', 5, 2, 0, 0),
(53, 'Mathematics_organizations', 5, 2, 0, 0),
(54, 'Mathematical_problems', 5, 2, 0, 0),
(55, 'Recreational_mathematics', 5, 2, 0, 0),
(56, 'Square_One_Television', 5, 2, 0, 0),
(57, 'Mathematics_websites', 5, 2, 0, 0),
(58, 'Elementary_algebra', 6, 2, 0, 0),
(59, 'Elementary_arithmetic', 6, 2, 0, 0),
(60, 'Elementary_geometry', 6, 2, 0, 0),
(61, 'Elementary_number_theory', 6, 2, 0, 0),
(62, 'Algebra', 7, 2, 0, 0),
(63, 'Mathematical_analysis', 7, 2, 0, 0),
(64, 'Applied_mathematics', 7, 2, 0, 0),
(65, 'Arithmetic', 7, 2, 0, 0),
(66, 'Calculus', 7, 2, 0, 0),
(67, 'Combinatorics', 7, 2, 0, 0),
(68, 'Computational_mathematics', 7, 2, 0, 0),
(69, 'Discrete_mathematics', 7, 2, 0, 0),
(70, 'Dynamical_systems', 7, 2, 0, 0),
(71, 'Elementary_mathematics', 7, 2, 0, 0),
(72, 'Experimental_mathematics', 7, 2, 0, 0),
(73, 'Foundations_of_mathematics', 7, 2, 0, 0),
(74, 'Game_theory', 7, 2, 0, 0),
(75, 'Geometry', 7, 2, 0, 0),
(76, 'Graph_theory', 7, 2, 0, 0),
(77, 'Mathematical_logic', 7, 2, 0, 0),
(78, 'Mathematics_of_infinitesimals', 7, 2, 0, 0),
(79, 'Number_theory', 7, 2, 0, 0),
(80, 'Order_theory', 7, 2, 0, 0),
(81, 'Probability_and_statistics', 7, 2, 0, 0),
(82, 'Recreational_mathematics', 7, 2, 0, 0),
(83, 'Representation_theory', 7, 2, 0, 0),
(84, 'Topology', 7, 2, 0, 0),
(85, 'Mathematics_by_culture', 8, 2, 0, 0),
(86, 'Mathematics_by_period', 8, 2, 0, 0),
(87, 'History_of_algebra', 8, 2, 0, 0),
(88, 'History_of_calculus', 8, 2, 0, 0),
(89, 'History_of_computer_science', 8, 2, 0, 0),
(90, 'History_of_geometry', 8, 2, 0, 0),
(91, 'Hilbert%27s_problems', 8, 2, 0, 0),
(92, 'Historians_of_mathematics', 8, 2, 0, 0),
(93, 'Historiography_of_mathematics', 8, 2, 0, 0),
(94, 'History_of_mathematics_journals', 8, 2, 0, 0),
(95, 'History_of_logic', 8, 2, 0, 0),
(96, 'Mathematics_manuscripts', 8, 2, 0, 0),
(97, 'Mathematical_problems', 8, 2, 0, 0),
(98, 'History_of_statistics', 8, 2, 0, 0),
(99, 'Mathematics_timelines', 8, 2, 0, 0),
(100, 'Algorithms', 10, 2, 0, 0),
(101, 'Mathematical_axioms', 10, 2, 0, 0),
(102, 'Majority', 10, 2, 0, 0),
(103, 'Mathematical_objects', 10, 2, 0, 0),
(104, 'Mathematical_structures', 10, 2, 0, 0),
(105, 'Mathematical_principles', 10, 2, 0, 0),
(106, 'Mathematical_relations', 10, 2, 0, 0),
(107, 'Basic_concepts_in_set_theory', 10, 2, 0, 0),
(108, 'Coordinate_systems', 12, 2, 0, 0),
(109, 'Mathematical_markup_languages', 12, 2, 0, 0),
(110, 'Mathematical_symbols', 12, 2, 0, 0),
(111, 'Numeral_systems', 12, 2, 0, 0),
(112, 'Mathematical_typefaces', 12, 2, 0, 0),
(113, 'Z_notation', 12, 2, 0, 0),
(114, 'Mathematical_logic', 13, 2, 0, 0),
(115, 'Mathematical_objects', 13, 2, 0, 0),
(116, 'Mathematics_and_mysticism', 13, 2, 0, 0),
(117, 'Mathematics_paradoxes', 13, 2, 0, 0),
(118, 'Philosophers_of_mathematics', 13, 2, 0, 0),
(119, 'Philosophy_of_computer_science', 13, 2, 0, 0),
(120, 'Structuralism_(philosophy_of_mathematics)', 13, 2, 0, 0),
(121, 'Theories_of_deduction', 13, 2, 0, 0),
(122, 'Algorithms', 14, 2, 0, 0),
(123, 'Applied_mathematics', 14, 2, 0, 0),
(124, 'Mathematical_principles', 14, 2, 0, 0),
(125, 'Mathematical_proofs', 14, 2, 0, 0),
(126, 'Mathematical_relations', 14, 2, 0, 0),
(127, 'Mathematical_tools', 14, 2, 0, 0),
(128, 'Mathematical_modeling', 14, 2, 0, 0),
(129, 'Numerical_analysis', 14, 2, 0, 0),
(130, 'Probability_and_statistics', 14, 2, 0, 0),
(131, 'Mathematical_problems', 14, 2, 0, 0),
(132, 'Mathematical_theorems', 14, 2, 0, 0),
(133, 'Dimension', 16, 2, 0, 0),
(134, 'Formal_methods_terminology', 16, 2, 0, 0),
(135, 'Calculators', 17, 2, 0, 0),
(136, 'Mathematical_markup_languages', 17, 2, 0, 0),
(137, 'Mathematical_software', 17, 2, 0, 0),
(138, 'Mathematical_tables', 17, 2, 0, 0),
(139, 'Cryptography_stubs', 18, 2, 0, 0),
(140, 'Mathematics_literature_stubs', 18, 2, 0, 0),
(141, 'Mathematician_stubs', 18, 2, 0, 0),
(142, 'Algebra_stubs', 18, 2, 0, 0),
(143, 'Mathematical_analysis_stubs', 18, 2, 0, 0),
(144, 'Applied_mathematics_stubs', 18, 2, 0, 0),
(145, 'Category_theory_stubs', 18, 2, 0, 0),
(146, 'Combinatorics_stubs', 18, 2, 0, 0),
(147, 'Mathematics_competition_stubs', 18, 2, 0, 0),
(148, 'Geometry_stubs', 18, 2, 0, 0),
(149, 'Mathematics_journal_stubs', 18, 2, 0, 0),
(150, 'Mathematical_logic_stubs', 18, 2, 0, 0),
(151, 'Number_theory_stubs', 18, 2, 0, 0),
(152, 'Number_stubs', 18, 2, 0, 0),
(153, 'Probability_stubs', 18, 2, 0, 0),
(154, 'Statistics_stubs', 18, 2, 0, 0),
(155, 'Topology_stubs', 18, 2, 0, 0);

-- --------------------------------------------------------

--
-- Structure de la table `wg_links`
--

CREATE TABLE IF NOT EXISTS `wg_links` (
  `id` int(100) NOT NULL AUTO_INCREMENT,
  `from` int(100) NOT NULL,
  `to` int(100) NOT NULL,
  `type` int(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `wg_page`
--

CREATE TABLE IF NOT EXISTS `wg_page` (
  `id` int(100) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `type` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
