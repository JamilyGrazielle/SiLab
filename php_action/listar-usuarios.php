<?php
header('Content-Type: application/json; charset=utf-8');
require_once 'db_connect.php';

try {
    $filtro = isset($_GET['filtro']) ? trim($_GET['filtro']) : '';

    $sql = "SELECT id, matricula, nome_completo, status, perfil 
            FROM Usuario";

    $params = [];

    if ($filtro !== '') {
        $sql .= " WHERE matricula LIKE :filtro OR nome_completo LIKE :filtro";
        $params[':filtro'] = "%{$filtro}%";
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($usuarios); // retorna só o array

} catch (PDOException $e) {
    echo json_encode([]);
    exit;
}
