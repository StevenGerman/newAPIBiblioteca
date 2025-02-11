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
    $sql = "LECT p.idPrestamo,p.presFechaDev,p.presFechaSal,p.presObservacion,p.personaID,p.libroID, personas.perNombre, personas.perApellido,l.libTitulo FROM prestamos as p INNER JOIN personas on p.personaID = personas.idPersona INNER JOIN libros as l ON p.libroID = l.idLibro;";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $datos = $stmt->fetchAll();
    echo json_encode($datos);
}




?>