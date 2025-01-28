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
}

// Maneja solicitudes GET
function handleGetRequest($pdo) {
    /*
    GET
Por ID (idAutor):
Parámetro: idEditorial=1
Retorno: Información del editorial con ID 1.
Por nombre (ediNombre):
Parámetro: ediNombre=Planeta
Retorno: Información de los editoriales cuyo nombre contenga "Planeta".
    
    */
    // Si se proporciona el parámetro 'idEditorial', busca por ID
    if (isset($_GET['idEditorial'])) {
        $sql = $pdo->prepare("SELECT * FROM Editoriales WHERE idEditorial=:idEditorial");
        $sql->bindValue(':idEditorial', $_GET['idEditorial']);
        $sql->execute();
        $sql->setFetchMode(PDO::FETCH_ASSOC);
        header("HTTP/1.1 200 OK");
        echo json_encode($sql->fetchAll());
    } 
    
    // Si se proporciona el autámetro 'ediNombre', busca por nombre
    elseif (isset($_GET['ediNombre'])) {
        $ediNombre = strtolower($_GET['ediNombre']);
        $sql = $pdo->prepare("SELECT * Editoriales WHERE LOWER(ediNombre) LIKE :ediNombre");
        $sql->bindValue(':ediNombre', '%' . $ediNombre . '%', PDO::PARAM_STR);
        $sql->execute();
        $sql->setFetchMode(PDO::FETCH_ASSOC);
        header("HTTP/1.1 200 OK");
        echo json_encode($sql->fetchAll());
    } 
    // Si no se proporciona ningún parámetro, obtiene todos las editoriales 
    else {
        $sql = $pdo->prepare("SELECT * FROM Editoriales");
        $sql->execute();
        $sql->setFetchMode(PDO::FETCH_ASSOC);
        header("HTTP/1.1 200 OK");
        echo json_encode($sql->fetchAll());
    }
    exit;
}

function handlePostRequest($pdo){
}
function handlePutRequest($pdo){
}
function handleDeleteRequest($pdo){
}








?>