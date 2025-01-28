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




match ($_SERVER['REQUEST_METHOD']) {
    'GET' => handleGetRequest($pdo),
    'POST' => handlePostRequest($pdo),
    'PUT' => handlePutRequest($pdo),
    'DELETE' => handleDeleteRequest($pdo),
    'OPTIONS' => header("HTTP/1.1 200 OK"),
    default => header("HTTP/1.1 405 Method Not Allowed"),
};

/*
switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        handleGetRequest($pdo);
        break;
    case 'POST':
        handlePostRequest($pdo);
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
}*/

function handleGetRequest($pdo){
    $sql = $pdo->prepare("SELECT * FROM Personas");
        $sql->execute();
        $sql->setFetchMode(PDO::FETCH_ASSOC);
        header("HTTP/1.1 200 OK");
        echo json_encode($sql->fetchAll());
    exit;
}

function handlePostRequest($pdo){

    $data = json_decode(file_get_contents("php://input"));
    if(isset($data->perNombre)){
        $sql = "INSERT INTO personas (perNombre,perApellido,perDni,perContrasena,rolID) values ((:perNombre),(:perApellido),(:perDni),(:perContrasena),(:rolID));";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':perNombre', $data->perNombre);
        $stmt->bindParam(':perApellido', $data->perApellido);
        $stmt->bindParam(':perDni', $data->perDni);
        $stmt->bindParam(':perContrasena', $data->perContrasena);
        $stmt->bindParam(':rolID', $data->rolID);

        if($stmt->execute()){
            $idPost = $pdo->lastInsertId();
            header("HTPP/1.1 201 Created");
            echo json_encode($idPost);//Restorna el ID de la persona creada.
        }else{
            header("HTPP/1.1 500 Internal Server Error");
            echo json_encode(['error' => 'No se pudo crear la persona']);
        }
    }else{
        header("HTTP/1.1 400 Bad Request");
        echo json_encode(['error' => 'Entrada inválida']);
    }
    exit;




}
function handlePutRequest($pdo){
}
function handleDeleteRequest($pdo){
}


?>