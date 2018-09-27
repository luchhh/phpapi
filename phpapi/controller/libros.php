<?php
include_once(__DIR__."/../../autoload.php");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

use Phpapi\Model\LibroRepositorio;
use Phpapi\Model\Libro;
use Phpapi\PaginadorREST;

$metodo = $_SERVER['REQUEST_METHOD'];
$cuerpo = json_decode(file_get_contents('php://input'),true);

switch($metodo){
        case "GET":
            $repo = new LibroRepositorio();
            $libros = $repo->buscar($_GET);
            if(sizeof($libros)==0){
                header("HTTP/1.0 404 Not Found");
                $respuesta["error"]["msg"] = "Ningún recurso encontrado";
                $respuesta["error"]["link"]["self"] = $_SERVER["REDIRECT_URL"];
                echo json_encode($respuesta);
            }

            foreach($libros as $libro){
                $respuesta["data"]["libros"][] = array(
                        "id"=>$libro["id"],
                        "nombre"=>$libro["name"],
                        "links"=>array(
                            "href"=>PHPAPI_URL."/libros/".$libro["id"],
                            "rel"=>"libros",
                            "type"=>"GET",
                        ),
                    );
            }

            $paginador = new PaginadorREST();
            $self = $paginador->query();
            $next = $paginador->next();
            $prev = $paginador->prev();

            $respuesta["data"]["links"] = array(
                array(
                    "rel"=>"self",
                    "href"=>$_SERVER["REDIRECT_URL"]."?".$self
                ),
            );
            if(isset($prev)){
                $respuesta["data"]["links"][] = array(
                        "rel"=>"prev",
                        "href"=>$_SERVER["REDIRECT_URL"]."?".$prev
                );
            }
            if(isset($next)){
                $respuesta["data"]["links"][] = array(
                            "rel"=>"next",
                            "href"=>$_SERVER["REDIRECT_URL"]."?".$next
                );
            }
            echo json_encode($respuesta);
        break;
        case "POST":
            $libro = new Libro();
            if(!$libro->crear($cuerpo["data"])){
                header("HTTP/1.0 400");
                $respuesta["error"]["msg"] = $libro->ultimoError();
                $respuesta["error"]["link"]["self"] = $_SERVER["REDIRECT_URL"];
                echo json_encode($respuesta);
                DIE;
            }
            $respuesta["data"] = $libro->getDetalle();
            echo json_encode($respuesta);
        break;
        case "PUT":
            //Metodo no permitido
            header("HTTP/1.0 405");
            $respuesta["error"]["msg"] = "Operación no permitida";
            $respuesta["error"]["link"]["self"] = $_SERVER["REDIRECT_URL"];
            echo json_encode($respuesta);
            DIE;
        break;
        case "DELETE":
            //Metodo no permitido
            header("HTTP/1.0 405");
            $respuesta["error"]["msg"] = "Operación no permitida";
            $respuesta["error"]["link"]["self"] = $_SERVER["REDIRECT_URL"];
            echo json_encode($respuesta);
            DIE;
        break;
}