<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Method: GET,POST,PUT,DELETE,OPTINS");
header("Content-Type: application/json; charset: UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers,Authorization, X-Requested-With");

include 'conexion.php';

$pdo = new Conexion();

switch ($_SERVER["REQUEST_METHOD"]) {
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

function getRequest($pdo)
{

    if (isset($_GET['idPrestamo'])) {
        $sql = "SELECT * FROM prestamos WHERE idPrestamo=:idPrestamo";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':idPrestamo', $_GET['idPrestamo']);
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

    } else {


        $sql = "SELECT p.idPrestamo,p.presFechaDev,p.presFechaSal,p.presObservacion,p.personaID,p.libroID, personas.perNombre, personas.perApellido,l.libTitulo FROM prestamos as p INNER JOIN personas on p.personaID = personas.idPersona INNER JOIN libros as l ON p.libroID = l.idLibro";
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
    exit;
}


function postRequest($pdo)
{
    $data = json_decode(file_get_contents('php://input'));

    $sql = "INSERT INTO prestamos (presFechaSal,presFechaDev,presObservacion,personaID,libroID) 
    VALUES (:presFechaSal,:presFechaDev,:presObservacion,:personaID,:libroID)";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':presFechaSal', $data->presFechaSal);
    $stmt->bindParam(':presFechaDev', $data->presFechaDev);
    $stmt->bindParam('presObservacion', $data->presObservacion);
    $stmt->bindParam(':personaID', $data->personaID);
    $stmt->bindParam(':libroID', $data->libroID);

    if ($stmt->execute()) {
        $idPost = $pdo->lastInsertId();
        header("HTTP/1.1 201 Created");
        echo json_encode($idPost);
    } else {
        header("HTTP/1.1 500 Internal Server Error");
        $errorInfo = $stmt->errorInfo(); // Get de info error
        echo json_encode(['error' => 'No se pudo crear la rol', 'details' => $errorInfo]); // Incluye detalles del error.
    }
}

function putRequest($pdo)
{
    $data = json_decode(file_get_contents('php//input'));

    $sql = "UPDATE prestamos SET presFechaSal=:presFechaSal, presFechaDev= :presFechaDev,
    presObservacion=:presObservacion, personaID=:personaID, libroID=:libroID
    WHERE idPrestamo=:idPrestamo";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':presFechaSal', $data->presFechaSal);
    $stmt->bindParam(':presFechaDev', $data->presFechaDev);
    $stmt->bindParam(':presObservacion', $data->presObservacion);
    $stmt->bindParam(':personaID', $data->personaID);
    $stmt->bindParam(':libroID', $data->libroID);
    $stmt->bindParam(':idPrestamo', $data->idPrestamo);

    if ($stmt->execute()) {
        header("HTTP/1.1 200 OK");
        echo json_encode(['message' => 'Actualización exitosa']);
    } else {

        header("HTTP/1.1 500 Server Error");
        echo json_encode(['message' => 'Error en servidor']);
    }
}



function deleteRequest($pdo)
{

    if (isset($_GET['idPrestamo'])) {
        $sql = "DELETE FROM prestamos WHERE idPrestamo=:idPrestamo";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':idPrestamo', $_GET['idPrestamo']);

        if ($stmt->execute()) {
            header("HTTP/1.1 200 OK");
            echo json_encode(['message' => 'Eliminación exitosa']);
        } else {
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode(['error' => 'No se pudo eliminar el prestamo']);
        }
    }
}
