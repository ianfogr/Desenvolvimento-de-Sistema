-- safeaudit_mysql.sql
-- Script para criar o banco de dados MySQL e as tabelas usadas pelo sistema.

CREATE DATABASE IF NOT EXISTS safeaudit
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE safeaudit;

-- Tabela de Usuários para autenticação
CREATE TABLE IF NOT EXISTS usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    perfil VARCHAR(50) NOT NULL DEFAULT 'tecnico',
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de Departamentos
CREATE TABLE IF NOT EXISTS departamentos (
    id_departamento INT AUTO_INCREMENT PRIMARY KEY,
    nome_departamento VARCHAR(100) NOT NULL UNIQUE,
    INDEX idx_nome_depart (nome_departamento)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de Categorias
CREATE TABLE IF NOT EXISTS categorias (
    id_categoria INT AUTO_INCREMENT PRIMARY KEY,
    nome_categoria VARCHAR(100) NOT NULL UNIQUE,
    descricao TEXT,
    INDEX idx_nome_categ (nome_categoria)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de Funcionários
CREATE TABLE IF NOT EXISTS funcionarios (
    id_funcionario INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    email VARCHAR(150) UNIQUE,
    telefone VARCHAR(20),
    INDEX idx_nome_func (nome),
    INDEX idx_email_func (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de Ativos (expandida)
CREATE TABLE IF NOT EXISTS ativos (
    id_ativo INT AUTO_INCREMENT PRIMARY KEY,
    nome_ativo VARCHAR(255) NOT NULL,
    tipo VARCHAR(100) NOT NULL,
    numero_patrimonio VARCHAR(50),
    data_aquisicao DATE,
    fk_responsavel INT,
    fk_departamento INT,
    fk_categoria INT,
    status VARCHAR(30) DEFAULT 'ativo',
    observacoes TEXT,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (fk_responsavel) REFERENCES funcionarios(id_funcionario) ON DELETE SET NULL ON UPDATE CASCADE,
    FOREIGN KEY (fk_departamento) REFERENCES departamentos(id_departamento) ON DELETE SET NULL ON UPDATE CASCADE,
    FOREIGN KEY (fk_categoria) REFERENCES categorias(id_categoria) ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX idx_nome_ativo (nome_ativo),
    INDEX idx_tipo (tipo),
    INDEX idx_status (status),
    INDEX idx_patrimonio (numero_patrimonio),
    UNIQUE KEY uk_patrimonio (numero_patrimonio)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de Manutenções
CREATE TABLE IF NOT EXISTS manutencoes (
    id_manutencao INT AUTO_INCREMENT PRIMARY KEY,
    fk_ativo INT NOT NULL,
    data_manutencao DATE NOT NULL,
    tipo_manutencao VARCHAR(100) NOT NULL,
    responsavel_manutencao VARCHAR(100) NOT NULL,
    observacoes TEXT,
    FOREIGN KEY (fk_ativo) REFERENCES ativos(id_ativo) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_ativo_manutencao (fk_ativo),
    INDEX idx_data_manutencao (data_manutencao)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dados de exemplo

-- Limpar dados antigos (se houver)
DELETE FROM manutencoes;
DELETE FROM ativos;
DELETE FROM usuarios;
DELETE FROM funcionarios;
DELETE FROM departamentos;
DELETE FROM categorias;

-- Usuários (senha padrão: admin123)
INSERT INTO usuarios (nome, email, senha, perfil) VALUES
('Administrador', 'admin@safeaudit.com', '$2y$10$9OkNmHeJpY3K1jZQwlZwhe0qY9KGqKQ2zSJ3jG7kD4ZK5QZM2hxPG', 'admin'),
('Técnico TI', 'tecnico@safeaudit.com', '$2y$10$9OkNmHeJpY3K1jZQwlZwhe0qY9KGqKQ2zSJ3jG7kD4ZK5QZM2hxPG', 'tecnico'),
('Gerente Administrativo', 'gerente@safeaudit.com', '$2y$10$9OkNmHeJpY3K1jZQwlZwhe0qY9KGqKQ2zSJ3jG7kD4ZK5QZM2hxPG', 'tecnico');

-- Departamentos
INSERT INTO departamentos (nome_departamento) VALUES
('TI'),
('Administrativo'),
('Financeiro'),
('Recursos Humanos');

-- Categorias
INSERT INTO categorias (nome_categoria, descricao) VALUES
('Servidores', 'Servidores físicos e virtuais'),
('Notebooks', 'Computadores portáteis'),
('Periféricos', 'Impressoras, scanners e similares'),
('Telefones', 'Telefones e smartphones');

-- Funcionários
INSERT INTO funcionarios (nome, email, telefone) VALUES
('Ana Silva', 'ana@empresa.com', '11999999999'),
('Bruno Santos', 'bruno@empresa.com', '11888888888'),
('Carlos Pereira', 'carlos@empresa.com', '11777777777'),
('Diana Costa', 'diana@empresa.com', '11666666666'),
('Eduardo Oliveira', 'eduardo@empresa.com', '11555555555');

-- Ativos
INSERT INTO ativos (nome_ativo, tipo, numero_patrimonio, data_aquisicao, fk_responsavel, fk_departamento, fk_categoria, status, observacoes) VALUES
('Servidor de produção', 'Servidor', 'PAT-001', '2023-01-15', 1, 1, 1, 'ativo', 'Servidor principal de produção'),
('Servidor de backup', 'Servidor', 'PAT-004', '2023-02-20', 1, 1, 1, 'ativo', 'Servidor de backup automático'),
('Notebook TI', 'Notebook', 'PAT-002', '2023-06-20', 2, 1, 2, 'ativo', 'Notebook para desenvolvimento'),
('Notebook Administrativo', 'Notebook', 'PAT-003', '2023-08-10', 3, 2, 2, 'ativo', 'Notebook para administrativo'),
('Notebook Financeiro', 'Notebook', 'PAT-005', '2023-09-15', 5, 3, 2, 'ativo', 'Notebook para análise financeira'),
('Impressora Laser', 'Impressora', 'PAT-006', '2023-03-05', 1, 1, 3, 'ativo', 'Impressora laser multifuncional'),
('Scanner de Documentos', 'Scanner', 'PAT-007', '2023-04-12', 4, 2, 3, 'ativo', 'Scanner automático de documentos'),
('Telefone IP Recepção', 'Telefone', 'PAT-008', '2023-05-20', 4, 2, 4, 'ativo', 'Telefone IP para recepção'),
('Switch de Rede', 'Switch', 'PAT-009', '2023-01-10', 1, 1, 3, 'manutenção', 'Switch gerenciável 48 portas');

-- Manutenções de exemplo
INSERT INTO manutencoes (fk_ativo, data_manutencao, tipo_manutencao, responsavel_manutencao, observacoes) VALUES
(1, '2024-01-10', 'Limpeza', 'Técnico Externo', 'Limpeza preventiva do servidor'),
(1, '2024-02-15', 'Atualização', 'Eduardo Oliveira', 'Atualização de BIOS'),
(3, '2024-01-20', 'Troca de HD', 'Bruno Santos', 'Substituição do disco rígido'),
(6, '2024-02-01', 'Manutenção', 'Técnico Externo', 'Limpeza e verificação de funcionamento'),
(9, '2024-03-01', 'Reparo', 'Bruno Santos', 'Reparação de porta ethernet danificada');
