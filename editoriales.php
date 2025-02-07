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

// Maneja la solicitud según el método HTTP
switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        getRequest($pdo);
        break;
    case 'POST':
        postRequest($pdo);
        break;
    case 'PUT':
        handlePutRequest($pdo);
        break;
    case 'DELETE':
        handleDeleteRequest($pdo);
        break;
    case 'OPTIONS':
        // Maneja las solicitudes preflight
        header("HTTP/1.1 200 OK");
        break;
    default:
        header("HTTP/1.1 405 Method Not Allowed");
        break;
}

// Maneja solicitudes GET
function GetRequest($pdo) {
        $sql = "SELECT * FROM editoriales";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        
        $datos = $stmt->fetchAll();
        

        if($datos){
            header("HTTP/1.1 200 OK");
            echo json_encode($datos);
        }else{
            //Error en el servidor
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode(array("error" => "Error en la base de datos"));
        } 
    
    exit;
}
function postRequest($pdo){
    $data = json_decode(file_get_contents('php://input'));

    $sql = "INSERT INTO editoriales (ediNombre,ediDireccion,ediTelefono,ediEmail) values ((:ediNombre),(:ediDireccion),(:ediTelefono),(:ediEmail));";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':ediNombre', $data->ediNombre);
    $stmt->bindParam(':ediDireccion', $data->ediDireccion);
    $stmt->bindParam(':ediTelefono', $data->ediTelefono);
    $stmt->bindParam(':ediEmail', $data->ediEmail);

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