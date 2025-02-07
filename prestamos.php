<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Method: GET,POST,PUT,DELETE,OPTINS");
header("Content-Type: application/json; charset: UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers,Authorization, X-Requested-With");

include 'conexion.php';

$pdo = new Conexion();

switch($_SERVER["REQUEST_METHOD"]){
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
        break;
}

function getRequest($pdo){
    $sql = "SELECT * FROM prestamos";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $datos = $stmt->fetchAll();
    echo json_encode($datos);
}

function postRequest($pdo);
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


?>