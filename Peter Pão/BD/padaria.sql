-- phpMyAdmin SQL Dump
-- version 4.8.0.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: 14-Maio-2018 às 03:13
-- Versão do servidor: 5.6.37
-- PHP Version: 5.6.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `padaria`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `userdatalogin`
--

CREATE TABLE `userdatalogin` (
  `cod` int(11) NOT NULL,
  `cpf` varchar(15) NOT NULL,
  `nome` varchar(50) NOT NULL,
  `senha` varchar(32) NOT NULL,
  `cargo` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `userdatalogin`
--

INSERT INTO `userdatalogin` (`cod`, `cpf`, `nome`, `senha`, `cargo`) VALUES
(1, '000.000.000-01', 'Antonio Jorge', '81dc9bdb52d04dc20036dbd8313ed055', 'Gerente'),
(2, '111.111.111-11', 'Karina Chata', 'c7bb043dde9ea54018e46824eba54fc4', 'Funcionário'),
(3, '222.222.222-22', 'Websley Phaphadon', '2020b1c37ce430cceb5cbfb872f44801', 'Padeiro');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `userdatalogin`
--
ALTER TABLE `userdatalogin`
  ADD PRIMARY KEY (`cod`),
  ADD UNIQUE KEY `cpf` (`cpf`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `userdatalogin`
--
ALTER TABLE `userdatalogin`
  MODIFY `cod` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
