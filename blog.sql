-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Tempo de geração: 01-Ago-2024 às 03:22
-- Versão do servidor: 10.4.28-MariaDB
-- versão do PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `blog`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `comentarios`
--

CREATE TABLE `comentarios` (
  `id_comentario` int(11) NOT NULL,
  `texto` text NOT NULL,
  `id_usuario_com` int(11) NOT NULL,
  `id_publi_com` int(11) NOT NULL,
  `data_comentario` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- --------------------------------------------------------

--
-- Estrutura da tabela `curtidas`
--

CREATE TABLE `curtidas` (
  `id_user_curt` int(11) NOT NULL,
  `id_publi_curt` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `links_arquivos`
--

CREATE TABLE `links_arquivos` (
  `id_link` int(11) NOT NULL,
  `id_publi` int(11) DEFAULT NULL,
  `link` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `publicacoes`
--

CREATE TABLE `publicacoes` (
  `id_publi` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `titulo` text NOT NULL,
  `conteudo` text NOT NULL,
  `descricao` text NOT NULL,
  `data_publicacao` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `publicacoes_salvas`
--

CREATE TABLE `publicacoes_salvas` (
  `id_publi_salvas` int(11) NOT NULL,
  `id_user_s` int(11) NOT NULL,
  `id_publi_s` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Estrutura da tabela `usuario`
--

CREATE TABLE `usuario` (
  `id_usuario` int(11) NOT NULL,
  `nome` text NOT NULL,
  `email` text NOT NULL,
  `senha` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuario_admin`
--

CREATE TABLE `usuario_admin` (
  `id_usuario_admin` int(11) NOT NULL,
  `email` text NOT NULL,
  `senha` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `usuario_admin`
--

INSERT INTO `usuario_admin` (`id_usuario_admin`, `email`, `senha`) VALUES
(1, 'admin@gmail.com', '12345');

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `comentarios`
--
ALTER TABLE `comentarios`
  ADD PRIMARY KEY (`id_comentario`),
  ADD KEY `id_publi` (`id_publi_com`),
  ADD KEY `id_user` (`id_usuario_com`);

--
-- Índices para tabela `curtidas`
--
ALTER TABLE `curtidas`
  ADD KEY `id_user_curt` (`id_user_curt`),
  ADD KEY `curtidas_publi` (`id_publi_curt`);

--
-- Índices para tabela `links_arquivos`
--
ALTER TABLE `links_arquivos`
  ADD PRIMARY KEY (`id_link`),
  ADD KEY `id_publi` (`id_publi`);

--
-- Índices para tabela `publicacoes`
--
ALTER TABLE `publicacoes`
  ADD PRIMARY KEY (`id_publi`);

--
-- Índices para tabela `publicacoes_salvas`
--
ALTER TABLE `publicacoes_salvas`
  ADD PRIMARY KEY (`id_publi_salvas`);

--
-- Índices para tabela `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_usuario`);

--
-- Índices para tabela `usuario_admin`
--
ALTER TABLE `usuario_admin`
  ADD PRIMARY KEY (`id_usuario_admin`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `comentarios`
--
ALTER TABLE `comentarios`
  MODIFY `id_comentario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1312;

--
-- AUTO_INCREMENT de tabela `links_arquivos`
--
ALTER TABLE `links_arquivos`
  MODIFY `id_link` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `publicacoes`
--
ALTER TABLE `publicacoes`
  MODIFY `id_publi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de tabela `publicacoes_salvas`
--
ALTER TABLE `publicacoes_salvas`
  MODIFY `id_publi_salvas` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT de tabela `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de tabela `usuario_admin`
--
ALTER TABLE `usuario_admin`
  MODIFY `id_usuario_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `comentarios`
--
ALTER TABLE `comentarios`
  ADD CONSTRAINT `id_publi` FOREIGN KEY (`id_publi_com`) REFERENCES `publicacoes` (`id_publi`),
  ADD CONSTRAINT `id_user` FOREIGN KEY (`id_usuario_com`) REFERENCES `usuario` (`id_usuario`);

--
-- Limitadores para a tabela `curtidas`
--
ALTER TABLE `curtidas`
  ADD CONSTRAINT `curtidas_publi` FOREIGN KEY (`id_publi_curt`) REFERENCES `publicacoes` (`id_publi`),
  ADD CONSTRAINT `curtidas_user` FOREIGN KEY (`id_user_curt`) REFERENCES `usuario` (`id_usuario`);

--
-- Limitadores para a tabela `links_arquivos`
--
ALTER TABLE `links_arquivos`
  ADD CONSTRAINT `links_arquivos_ibfk_1` FOREIGN KEY (`id_publi`) REFERENCES `publicacoes` (`id_publi`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
