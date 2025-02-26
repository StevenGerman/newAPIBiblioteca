<?php

// Habilita el acceso desde cualquier origen
header("Access-Control-Allow-Origin: *");
// Habilita los métodos HTTP permitidos
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
// Establece el tipo de contenido de la respuesta como JSON
header("Content-Type: application/json; charset=UTF-8");
// Habilita las cabeceras permitidas
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once 'conexion.php';

require_once 'auth.php';


require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
$key = $_ENV['SECRET_KEY']; 

$pdo = new Conexion();
$auth = new Autorization($key);




switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        getRequest($pdo,$auth);
        break;
    case 'POST':
        postRequest($pdo,$auth);
        break;
    case 'PUT':
        putRequest($pdo);
        break;
    case 'DELETE':
        deleteRequest($pdo,$auth);
        break;
    case 'OPTIONS':
        // Maneja las solicitudes preflight
        header("HTTP/1.1 200 OK");
        break;
    default:
        header("HTTP/1.1 405 Method Not Allowed");
        break;
}


function getRequest($pdo,$auth){
    if(!$auth->validateToken($pdo)){
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
        if($datos){
            header("HTTP/1.1 200 OK");
            echo json_encode($datos);
        
        }else{
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode(array("error" => "Error en el servidor"));
        }
    }
 
}


function postRequest($pdo,$auth){
    if(!$auth->validateToken($pdo)){
        http_response_code(403);
        echo json_encode(array("error" => "Unauthorized"));
        return;
    }
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

function deleteRequest($pdo,$auth){
    if(!$auth->validateToken($pdo)){
        http_response_code(403);
        echo json_encode(array("error" => "Unauthorized"));
        return;
    }
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