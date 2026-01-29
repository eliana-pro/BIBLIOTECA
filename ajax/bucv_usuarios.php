<?php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../repositories/bucv_1cc2s4B3.php';

$repo = new BucvRepository();

$method = $_SERVER['REQUEST_METHOD'];

try {

    // 🔹 GET → listar usuarios
    if ($method === 'GET') {

        $usuarios = $repo->obtenerUsuarios();

        echo json_encode([
            'status' => 'ok',
            'total' => count($usuarios),
            'data' => $usuarios
        ]);
        exit;
    }

    // 🔹 POST → crear usuario
    if ($method === 'POST') {

        $input = json_decode(file_get_contents("php://input"), true);

        if (
            empty($input['id_rol']) ||
            empty($input['nombre']) ||
            empty($input['correo']) ||
            empty($input['password'])
        ) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Datos incompletos'
            ]);
            exit;
        }

        $passwordHash = password_hash($input['password'], PASSWORD_BCRYPT);

        $idUsuario = $repo->crearUsuario(
            $input['id_rol'],
            $input['nombre'],
            $input['correo'],
            $passwordHash
        );

        echo json_encode([
            'status' => 'ok',
            'message' => 'Usuario creado correctamente',
            'id_usuario' => $idUsuario
        ]);
        exit;
    }

    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'message' => 'Método no permitido'
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
