<?php
// Usa o mesmo arquivo de conexão do projeto (usando PDO)
require_once __DIR__ . '/db_connect.php';

// Lista de usuários a serem inseridos
$usuarios = [
    ['nome' => 'Jamily Grazielle Sousa Maciel', 'matricula' => '20231SI0022', 'perfil' => 'adm'],
    ['nome' => 'Kaillane Corrêa Martins', 'matricula' => '20211SI0023', 'perfil' => 'adm'],
    ['nome' => 'Franciele Alves da Silva', 'matricula' => '20231SI0012', 'perfil' => 'Professor'],
    ['nome' => 'Victor José Quaresma Martins', 'matricula' => '20221SI0002', 'perfil' => 'Professor'],
    ['nome' => 'Angelo Pacheco', 'matricula' => '20241SI0017', 'perfil' => 'Professor'],
    ['nome' => 'Rianderson Pereira Correa', 'matricula' => '20241SI0002', 'perfil' => 'Professor'],
    ['nome' => 'Raquel Coelho', 'matricula' => '20231SI0018', 'perfil' => 'Professor'],
    ['nome' => 'Felipe Moura de Oliveira', 'matricula' => '20231SI0027', 'perfil' => 'Professor'],
    ['nome' => 'Augusto Jose Santos Nascimento', 'matricula' => '20241SI0008', 'perfil' => 'Professor'],
    ['nome' => 'Renan Moreira da Silva', 'matricula' => '20241SI0023', 'perfil' => 'Professor'],
    ['nome' => 'Otávio Augusto Correa da Silva', 'matricula' => '20241SI0025', 'perfil' => 'Professor'],
    ['nome' => 'Samuel Chaves De Sá', 'matricula' => '20231SI0011', 'perfil' => 'Professor'],
    ['nome' => 'Heloize Mafra Coelho Reis', 'matricula' => '20232SI0024', 'perfil' => 'Professor'],
    ['nome' => 'Deusiane Pimenta Rocha', 'matricula' => '20231SI0032', 'perfil' => 'Professor'],
    ['nome' => 'Felipe Silva Matos', 'matricula' => '20231SI0029', 'perfil' => 'Professor'],
    ['nome' => 'Laertty Lima Bizerra', 'matricula' => '20231SI0016', 'perfil' => 'adm']
];

// Loop para inserir cada usuário
foreach ($usuarios as $usuario) {
    try {
        // Extrai os dois últimos dígitos da matrícula
        $ultimosDigitos = substr($usuario['matricula'], -2);

        // Pega o primeiro nome
        $primeiroNome = explode(' ', $usuario['nome'])[0];

        // Gera a senha no formato desejado
        $senha = $primeiroNome . '@' . $ultimosDigitos;
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

        // Prepara o SQL com status 'aprovado'
        $sql = "INSERT INTO Usuario (matricula, nome_completo, senha, status, perfil)
                VALUES (:matricula, :nome_completo, :senha, 'aprovado', :perfil)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':matricula', $usuario['matricula']);
        $stmt->bindParam(':nome_completo', $usuario['nome']);
        $stmt->bindParam(':senha', $senha_hash);
        $stmt->bindParam(':perfil', $usuario['perfil']);

        $stmt->execute();
        echo "Usuário <strong>{$usuario['nome']}</strong> cadastrado com sucesso! 
              Senha: <code>$senha</code><br>";

    } catch (PDOException $e) {
        echo "❌ Erro ao cadastrar <strong>{$usuario['nome']}</strong>: " . $e->getMessage() . "<br>";
    }
}
?>
