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

function getRequest($pdo){
    $sql = $pdo->prepare("SELECT l.idLibro,l.libTitulo,l.libAnio,l.libNotaDeContenido,e.idEditorial,e.ediNombre,m.idMateria,m.matNombre,a.idAutor,a.autNombre,a.autApellido FROM libros as l INNER JOIN editoriales as e ON l.idLibro = e.idEditorial INNER JOIN materias AS m ON l.materiaID = m.idMateria INNER JOIN autores as a on l.autorID = a.idAutor");
        $sql->execute();
        $sql->setFetchMode(PDO::FETCH_ASSOC);
        header("HTTP/1.1 200 OK");
        echo json_encode($sql->fetchAll());
    exit;
}



?>