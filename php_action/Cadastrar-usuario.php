<?php
include('conexao.php');

// Array com todos os usuários
$usuarios = [
    [
        'nome' => 'Jamily Grazielle Sousa Maciel',
        'matricula' => '20231SI0022',
        'perfil' => 'adm'
    ],
    [
        'nome' => 'Kaillane Corrêa Martins',
        'matricula' => '20211SI0023', 
        'perfil' => 'adm'
    ],
    [
        'nome' => 'Franciele Alves da Silva',
        'matricula' => '20231SI0012',
        'perfil' => 'Professor'
    ],
    [
        'nome' => 'Victor José Quaresma Martins',
        'matricula' => '20221SI0002',
        'perfil' => 'Professor'
    ],
    [
        'nome' => 'Angelo Pacheco',
        'matricula' => '20241SI0017',
        'perfil' => 'Professor'
    ],
    [
        'nome' => 'Rianderson Pereira Correa',
        'matricula' => '20241SI0002',
        'perfil' => 'Professor'
    ],
    [
        'nome' => 'Raquel Coelho',
        'matricula' => '20231SI0018',
        'perfil' => 'Professor'
    ],
    [
        'nome' => 'Felipe Moura de Oliveira',
        'matricula' => '20231SI0027',
        'perfil' => 'Professor'
    ],
    [
        'nome' => 'Augusto Jose Santos Nascimento',
        'matricula' => '20241SI0008',
        'perfil' => 'Professor'
    ],
    [
        'nome' => 'Renan Moreira da Silva',
        'matricula' => '20241SI0023',
        'perfil' => 'Professor'
    ],
    [
        'nome' => 'Otávio Augusto Correa da Silva',
        'matricula' => '20241SI0025',
        'perfil' => 'Professor'
    ],
    [
        'nome' => 'Samuel Chaves De Sá',
        'matricula' => '20231SI0011',
        'perfil' => 'Professor'
    ],
    [
        'nome' => 'Heloize Mafra Coelho Reis',
        'matricula' => '20232SI0024',
        'perfil' => 'Professor'
    ],
    [
        'nome' => 'Deusiane Pimenta Rocha',
        'matricula' => '20231SI0032',
        'perfil' => 'Professor'
    ],
    [
        'nome' => 'Felipe Silva Matos',
        'matricula' => '20231SI0029',
        'perfil' => 'Professor'
    ],
    [
        'nome' => 'Laertty Lima Bizerra',
        'matricula' => '20231SI0016',
        'perfil' => 'adm'
    ]
];

// Loop para inserir cada usuário
foreach ($usuarios as $usuario) {
    // Extrai os últimos 2 dígitos da matrícula
    $ultimosDigitos = substr($usuario['matricula'], -2);
    
    // Pega o primeiro nome (antes do primeiro espaço)
    $primeiroNome = explode(' ', $usuario['nome'])[0];
    
    // Cria a senha no formato "PrimeiroNome@Ultimos2Digitos"
    $senha = $primeiroNome . '@' . $ultimosDigitos;
    
    // Gera o hash da senha
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
    
    // Prepara a query SQL
    $sql = "INSERT INTO Usuario (matricula, nome_completo, senha, status, perfil)
            VALUES (?, ?, ?, 'ativo', ?)";
    
    // Usando prepared statements para segurança
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("ssss", 
        $usuario['matricula'],
        $usuario['nome'],
        $senha_hash,
        $usuario['perfil']
    );
    
    // Executa e verifica
    if ($stmt->execute()) {
        echo "Usuário {$usuario['nome']} cadastrado com sucesso! Senha: $senha<br>";
    } else {
        echo "Erro ao cadastrar {$usuario['nome']}: " . $mysqli->error . "<br>";
    }
    
    $stmt->close();
}

$mysqli->close();
?>