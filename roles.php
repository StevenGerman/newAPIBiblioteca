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

switch($_SERVER['REQUEST_METHOD']){
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
    default:
        header("HTTP/1.1 405 Method Not Allowed");

}

function getRequest($pdo){
    $sql = "SELECT * FROM roles";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    header("HTTP/1.1 200 OK");
    echo json_encode($stmt->fetchAll());
}

function postRequest($pdo){
    $data = json_decode(file_get_contents('php://input'));

    $sql = "INSERT INTO roles (rolNombre) VALUES (:rolNombre)"; // Corrected SQL
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':rolNombre', $data->matNombre); // Correct binding

    if($stmt->execute()){
        $idPost = $pdo->lastInsertId();
        header("HTTP/1.1 201 Created");
        echo json_encode($idPost);
    }else{
        header("HTTP/1.1 500 Internal Server Error"); // More appropriate status code
        $errorInfo = $stmt->errorInfo(); // Get detailed error information
        echo json_encode(['error' => 'No se pudo crear la rol', 'details' => $errorInfo]); // Include details for debugging
    }
}

function deleteRequest($pdo){
    if(isset($_GET['idRol'])){
        $sql = "DELETE FROM roles WHERE idRol = :idRol";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':idRol',$_GET['idRol']);
        if($stmt->execute()){
            header("HTTP/1.1 200 OK");
            echo json_encode(['message' => 'Eliminación exitosa']); // Retorna un mensaje de éxito
        }else{
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode(['error' => 'No se pudo eliminar el roles']);
        }
    }else {
        header("HTTP/1.1 400 Bad Request");
        echo json_encode(['error' => 'Entrada inválida']);
    }
    exit;

}




?>