<?php
include_once(__DIR__."/../../autoload.php");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

use Phpapi\Model\Libro;

$metodo = $_SERVER['REQUEST_METHOD'];
$cuerpo = json_decode(file_get_contents('php://input'), true);

switch($metodo){
        case "GET":
            $libro = new Libro();
            if(!$libro->cargar($_GET["id"])){
                header("HTTP/1.0 404 Not Found");
                $respuesta["error"]["msg"] = "Recurso ".$_GET["id"]." no encontrado";
                $respuesta["error"]["link"]["self"] = $_SERVER["REDIRECT_URL"];
                echo json_encode($respuesta);
                DIE;
            }
            $respuesta["data"] = $libro->getDetalle();
            echo json_encode($respuesta);
        break;
        case "POST":
            //Metodo no permitido
            header("HTTP/1.0 405");
            $respuesta["error"]["msg"] = "Operación no permitida";
            $respuesta["error"]["link"]["self"] = $_SERVER["REDIRECT_URL"];
            echo json_encode($respuesta);
            DIE;
        break;
        case "PUT":
            $libro = new \Phpapi\Model\Libro();
            if(!$libro->cargar($_GET["id"])){
                header("HTTP/1.0 404");
                $respuesta["error"]["msg"] = "Recurso ".$_GET["id"]." no encontrado";
                $respuesta["error"]["link"]["self"] = $_SERVER["REDIRECT_URL"];
                echo json_encode($respuesta);
                DIE;
            }
            if(!$libro->actualizar($cuerpo["data"])){
                header("HTTP/1.0 400");
                $respuesta["error"]["msg"] = $libro->ultimoError();
                $respuesta["error"]["link"]["self"] = $_SERVER["REDIRECT_URL"];
                echo json_encode($respuesta);
                DIE;
            }
            $respuesta["data"] = $libro->getDetalle();
            echo json_encode($respuesta);
        break;
        case "DELETE":
            $id = filter_var($_GET["id"], FILTER_SANITIZE_NUMBER_INT);
            $libro = new Libro();
            if(!$libro->cargar($id)){
                header("HTTP/1.0 404");
                DIE;
            }
            //eliminar objeto pdte
        break;
}