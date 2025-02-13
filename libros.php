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

function getRequest($pdo)
{
    if (isset($_GET['idLibro'])) {
        $sql = "SELECT * FROM libros WHERE idLibro = :idLibro";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':idLibro', $_GET['idLibro']);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $datos = $stmt->fetchAll();
        if ($datos) {
            header("HTTP/1.1 200 OK");
            echo json_encode($datos);
        } else {
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode(array("error" => "Error en el servidor"));
        }
    } else {
        $sql = $pdo->prepare("SELECT l.idLibro,l.libTitulo,l.libAnio,l.libNotaDeContenido,e.idEditorial,e.ediNombre,m.idMateria,m.matNombre,a.idAutor,a.autNombre,a.autApellido FROM libros as l INNER JOIN editoriales as e ON l.idLibro = e.idEditorial INNER JOIN materias AS m ON l.materiaID = m.idMateria INNER JOIN autores as a on l.autorID = a.idAutor");
        $sql->execute();
        $sql->setFetchMode(PDO::FETCH_ASSOC);
        header("HTTP/1.1 200 OK");
        echo json_encode($sql->fetchAll());
    }

    exit;
}


function postRequest($pdo)
{
    $data = json_decode(file_get_contents('php//input'));

    if ($data->libTitulo) {
        $sql = "INSERT INTO libros (libTitulo,libAnio,libNotaDeContenido,editorialID,materiaID,autorID)
            values (:libTitulo,:libAnio,:libNotaDeContenido,:editorialID,:materiaID,:autorID)";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':libTitulo', $data->libTitulo);
        $stmt->bindParam(':libAnio', $data->libAnio);
        $stmt->bindParam(':libNotaDeContenido', $data->libNotaDeContenido);
        $stmt->bindParam(':editorialID', $data->editorialID);
        $stmt->bindParam(':materiaID', $data->materiaID);
        $stmt->bindParam(':autorID', $data->autorID);

        if ($stmt->execute()) {
            $idPost = $pdo->lastInsertId();
            header("HTTP/1.1 201 Created");
            echo json_encode($idPost);
        } else {
            header("HTTP/1.1 500 Error Server");
            echo json_encode(['error' => 'No se pudo crear la editorial']);
        }
    } else {
        header("HTTP/1.1 405 Bad Request");
        echo json_encode(['message' => 'Error en la consulta']);
    }
    exit;
}

function putRequest($pdo){
    $data = json_decode(file_get_contents('php//input'));

    if($data->idLibro){
        $sql = "UPDATE libros SET libTitulo = :libTitulo, libAnio = :libAnio, libNotaDeContenido = :libNotaDeContenido, 
        editorialID = :editorialID, materiaID = :materiaID, autorID = :autorID ";

        $stmt = $pdo->prepare($sql);
        $stmt -> bindParam('libTitulo', $data->libTitulo);
        $stmt -> bindParam('libAnio', $data->libAnio);
        $stmt -> bindParam('libNotaDeContenido', $data->libNotaDeContenido);
        $stmt -> bindParam('editorialID',$data->editorialID);
        $stmt -> bindParam('materiaID', $data->materiaID);
        $stmt -> bindParam('autorID', $data->autorID);

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


    if(isset($_GET['idLibro'])){
        $sql = "DELETE FROM libros WHERE idLibro = :idLibro";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':idLibro',$_GET['idLibro']);
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
