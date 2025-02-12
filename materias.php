<?php

// Habilita el acceso desde cualquier origen
header("Access-Control-Allow-Origin: *");
// Habilita los métodos HTTP permitidos
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
// Establece el tipo de contenido de la respuesta como JSON
header("Content-Type: application/json; charset=UTF-8");
// Habilita las cabeceras permitidas
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include 'conexion.php';

$pdo = new Conexion();


switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        getRequest($pdo);
        break;
    case 'POST':
        postRequest($pdo);
        break;
    case 'PUT':
        putRequest($pdo);
        break;
    case 'DELETE':
        deleteRequest($pdo);
        break;
    case 'OPTIONS':
        // Maneja las solicitudes preflight
        header("HTTP/1.1 200 OK");
        break;
    default:
        header("HTTP/1.1 405 Method Not Allowed");
        break;
}


function getRequest($pdo){
    $sql = "SELECT * FROM materias";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    header("HTTP/1.1 200 OK");
    echo json_encode($stmt->fetchAll());
}


function postRequest($pdo){
    $data = json_decode(file_get_contents('php://input'));

    $sql = "INSERT INTO materias (matNombre) values :ediNombre";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':matNombre', $data->matNombre);

    if($stmt->execute()){
        $idPost = $pdo->lastInsertId();
        header("HTTP/1.1 201 Created");
        echo json_encode($idPost);
    }else{
        header("HTTP/1.1 500 Error Server");
        echo json_encode(['error' => 'No se pudo crear la editorial']);
    }
}


?>