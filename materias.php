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

require __DIR__ . '/vendor/autoload.php';
$pdo = new Conexion();

use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;




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


function getToken(){
    $headers = apache_request_headers();
    if(!isset($headers['Authorization'])){
        http_response_code(403);
        echo json_encode(array("error" => "Token invalid"));
        return;
    }
    $authorization = $headers['Authorization'];
    $authorization = explode(' ', $authorization);
    $token = $authorization[1];

    try{
        return JWT::decode($token, new Key('example_key', 'HS256'));

    }catch(Exception $e){
        http_response_code(403);
        echo json_encode(array("error" => "Expired Token"));
        return;
    }

    
}

function validateToken($pdo){
    $info = getToken();
    if($info == null){
        return;
    }else{
        $sql = "SELECT * FROM personas WHERE idPersona = :idPersona";
        $stmt = $pdo->prepare($sql);
        $idPersona = $info->data->idPersona;
        //echo json_encode($info->data->idPersona);
        $stmt->bindParam(':idPersona', $idPersona);
        $stmt -> execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $dataValidada = $stmt->fetch();
        if($dataValidada == null){
            return false;
        }
        return $dataValidada['idPersona'];
    }
    
    
}


function getRequest($pdo){
    if(!validateToken($pdo)){
        http_response_code(403);
        echo json_encode(array("error" => "Unauthorized"));
        return;
    }
  
    if(isset($_GET['idMateria'])){
        $sql = "SELECT * FROM materias WHERE idMateria = :idMateria";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':idMateria', $_GET['idMateria']);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $datos = $stmt->fetchAll();
        if($datos){
            header("HTTP/1.1 200 OK");
            echo json_encode($datos);
        }else{
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode(array("error" => "Error en el servidor"));
        }
    }else{
        $sql = "SELECT * FROM materias";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $datos = $stmt->fetchAll();
        $validate = validateToken($pdo);
        if($datos){
            header("HTTP/1.1 200 OK");
            echo json_encode([
                "datos" => $datos
            ]);
        }else{
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode(array("error" => "Error en el servidor"));
        }
    }
 
}


function postRequest($pdo){
    $data = json_decode(file_get_contents('php://input'));

    $sql = "INSERT INTO materias (matNombre) VALUES (:matNombre)"; // Corrected SQL
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':matNombre', $data->matNombre); // Correct binding

    if($stmt->execute()){
        $idPost = $pdo->lastInsertId();
        header("HTTP/1.1 201 Created");
        echo json_encode($idPost);
    }else{
        header("HTTP/1.1 500 Internal Server Error"); 
        $errorInfo = $stmt->errorInfo(); 
        echo json_encode(['error' => 'No se pudo crear la materia', 'details' => $errorInfo]); // Include details for debugging
    }
}

function deleteRequest($pdo){

    if(isset($_GET['idMateria'])){
        $sql = "DELETE FROM materias WHERE idMateria = :idMateria";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':idMateria',$_GET['idMateria']);
        if($stmt->execute()){
            header("HTTP/1.1 200 OK");
            echo json_encode(['message' => 'Eliminación exitosa']); // Retorna un mensaje de éxito
        }else{
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode(['error' => 'No se pudo eliminar el editorial']);
        }
    }else {
        header("HTTP/1.1 400 Bad Request");
        echo json_encode(['error' => 'Entrada inválida']);
    }
    exit;

}








?>