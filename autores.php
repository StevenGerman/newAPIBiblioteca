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
        break;
}


function getRequest($pdo){
    
    if($_GET['idAutor']){
        $sql = "SELECT * FROM autores WHERE idAutor = :idAutor";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam('idAutor',$_GET['idAutor']);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $datos = $stmt->fetchAll();
        if($datos){
            header("HTTP/1.1 200 OK");
            echo json_encode($datos);
        }else{
            //Error en el servidor
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode(array("error" => "Error en el servidor"));
        } 
    }else{
        $sql = "SELECT * FROM autores";
        $stmt =  $pdo->prepare($sql);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $datos = $stmt->fetchAll();
        if($datos){
            header("HTTP/1.1 200 OK");
            echo json_encode($datos);
        }else{
            //Error en el servidor
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode(array("error" => "Error en el servidor"));
        } 
    }
}

function postRequest($pdo){
    $data = json_decode(file_get_contents('php//input'));

    if(isset($data->autNombre)){
        $sql = "INSERT INTO autores (autNombre,autApellido,autFechaNac,autFechaDes,autBiografia)
        values (:autNombre,:autApellido,:autFechaNac,:autFechaDes,:autBiografia)";
        $stmt = $pdo->prepare($sql);
        $stmt -> bindParam(':autNombre',$data->autNombre);
        $stmt -> bindParam(':autApellido', $data->autApellido);
        $stmt -> bindParam(':autFechaNac', $data->autFechaNac);
        $stmt -> bindParam(':autFechaDes', $data->autFechaDes);
        $stmt -> bindParam(':autBiografia', $data->autBiografia);

        if($stmt->execute()){
            $idPost = $pdo->lastInsertId();
            header("HTTP/1.1 201 Created");
            echo json_encode($idPost);
        }else{
            header("HTTP/1.1 500 Error Server");
            echo json_encode(['error' => 'No se pudo crear la editorial']);
        }
    }

}
function putRequest($pdo){

    $data = json_decode(file_get_contents('php//input'));

    if(isset($data->idAutor)){
        $sql = "UPDATE autores SET autNombre = :autNombre, autApellido = :autApellido, 
        autFechaNac = :autFechaDes, autBiografia = :autBiografia WHERE idAutor = :idAutor";

        $stmt = $pdo->prepare($pdo);
        $stmt -> bindParam(':autNombre', $data->autNombre);
        $stmt -> bindParam(':autApellido', $data->autApellido);
        $stmt -> bindParam(':autFechaNac', $data->autFechaNac);
        $stmt -> bindParam(':autFechaDes', $data->autFechaDes);
        $stmt -> bindParam(':autBiografia', $data->autBiografia);
        $stmt -> bindParam('idAutor', $data->idAutor);

        if($stmt->execute()){
            header("HTTP/1.1 200 OK");
            echo json_encode(['message' => 'Actualización exitosa']);
        }else{
            header("HTTP/1.1 500 Server Error");
            echo json_encode(['message' => 'Error en servidor']);
        }
    }else{
        header("HTTP/1.1 405 Bad Request");
            echo json_encode(['message' => 'Error en la consulta']);

    }
    exit;

}

function deleteRequest($pdo){


    if(isset($_GET['idAutor'])){
        $sql = "DELETE FROM autores WHERE idAutor = :idAutor";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':idAutor',$_GET['idAutor']);
        if($stmt->execute()){
            header("HTTP/1.1 200 OK");
            echo json_encode(['message' => 'Eliminación exitosa']);
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