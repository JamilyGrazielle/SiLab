Drop database if exists silab;
create database silab;
use silab;

CREATE TABLE Usuario (
	id INT AUTO_INCREMENT PRIMARY KEY,
    matricula VARCHAR(20),
    nome_completo VARCHAR(70),
    senha VARCHAR(255),
    status enum('ativo', 'inativo'),
    perfil ENUM('Professor', 'adm')
);

CREATE TABLE Laboratorio (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) UNIQUE NOT NULL,
    capacidade INT NOT NULL
);

CREATE TABLE Equipamento (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) UNIQUE NOT NULL
);

CREATE TABLE Laboratorio_Equipamento (
    laboratorio_id INT,
    equipamento_id INT,
    quantidade INT NOT NULL,
    PRIMARY KEY (laboratorio_id, equipamento_id),
    FOREIGN KEY (laboratorio_id) REFERENCES Laboratorio(id) ON DELETE CASCADE,
    FOREIGN KEY (equipamento_id) REFERENCES Equipamento(id) ON DELETE CASCADE
);

CREATE TABLE Disciplina (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL
);

CREATE TABLE Horario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    descricao VARCHAR(50) NOT NULL -- Ex: "08:00 - 10:00"
);

INSERT INTO Horario (descricao) VALUES 
('08:00 - 08:49'),
('08:50 - 09:39'),
('10:00 - 10:49'),
('10:50 - 11:39'),
('14:00 - 14:49'),
('14:50 - 15:39'),
('15:55 - 16:44'),
('16:45 - 17:34'),
('17:35 - 18:25'),
('18:30 - 19:19'),
('19:20 - 20:09'),
('20:10 - 20:59'),
('21:00 - 21:49'),
('21:50 - 22:40');



CREATE TABLE Reserva (
    id INT AUTO_INCREMENT PRIMARY KEY,
    laboratorio_id INT NOT NULL,
    usuario_id INT NOT NULL,
    data_reserva DATE NOT NULL,
    hora_inicio TIME NOT NULL,
    hora_fim TIME NOT NULL,
    motivo TEXT,
    status ENUM('pendente', 'confirmada', 'cancelada') DEFAULT 'pendente',
    FOREIGN KEY (laboratorio_id) REFERENCES Laboratorio(id),
    FOREIGN KEY (usuario_id) REFERENCES Usuario(id)
);



