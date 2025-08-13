<?php
$host = 'localhost';
$dbname = 'silab';
$username = 'root'; // altere conforme necessário
$password = '';     // altere conforme necessário

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Verificar se a tabela Usuario está vazia e criar admin root
    $stmt = $pdo->query("SELECT COUNT(*) AS total FROM Usuario WHERE matricula = '20251DC0000' and perfil = 'adm'");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result['total'] == 0) {
        $senha_hash = password_hash('Root@00', PASSWORD_DEFAULT);
        $sql = "INSERT INTO Usuario 
                (matricula, nome_completo, email, senha, perfil, status) 
                VALUES 
                ('20251DC0000', 'root', 'Dcomp@acad.ifma.edu.br', ?, 'adm', 'aprovado')";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$senha_hash]);
        
        error_log("Usuário root criado automaticamente");
    }

} catch (PDOException $e) {
    // Detectar se o request veio de uma chamada AJAX/API
    $is_api_call = (
        stripos($_SERVER['REQUEST_URI'], '/php_action/') !== false ||
        (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)
    );

    if ($is_api_call) {
        // Responder em JSON para evitar erro no fetch
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'error' => true,
            'message' => 'Erro na conexão com o banco de dados: ' . $e->getMessage()
        ]);
        exit;
    } else {
        // Caso seja acesso direto pelo navegador
        die("Erro na conexão com o banco de dados: " . $e->getMessage());
    }
}
