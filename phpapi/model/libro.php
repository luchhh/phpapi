<?php
namespace Phpapi\Model;
use Phpapi\DB;
use PDO;

class Libro{
 
    //nombre de la tabla en BD
    private $tabla = "libro";
 
    //propiedades del objeto
    public $id;
    public $nombre;
    public $descripcion;
    public $url;
    public $creada;
    public $modificada;    
    private $error;

    //constructor vacio
    public function __construct(){
    }

    //carga los datos de un objeto
    function cargar($id=null){

        if($id!=null){
            $this->id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);
        }

        //consulta para leer una fila
        $query =    "SELECT p.id, p.name, p.description, p.url, p.created, p.modified
                     FROM ".$this->tabla." p
                     WHERE
                        p.id = ?
                     LIMIT
                        0,1";

        //preparar consulta
        $conn = DB::get()->conn();
        $stmt = $conn->prepare($query);
        //reemplazar el parámetro id de la consulta
        $stmt->bindParam(1, $this->id);
        //ejecutar consulta
        $stmt->execute();

        //validar si existe el objeto
        if($stmt->rowCount() == 0){
            $this->id = NULL;
            return false;
        }

        //obtener columna
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        //asignar los valores al objeto this
        $this->nombre = $row['name'];
        $this->descripcion = $row['description'];
        $this->url = $row['url'];        
        $this->creada = $row['created'];
        $this->modificada = $row['modified'];
        return true;
    }

    //actualiza los datos de un objeto
    function actualizar($datos){

        //PDO se encarga de evitar SQL injection via bindParam
        //Solo hace falta validar reglas de dominio
        if(isset($datos["url"])){
            $datos["url"] = filter_var($datos["url"], FILTER_SANITIZE_URL);
            if(!filter_var($datos["url"], FILTER_VALIDATE_URL)){
                $this->error = "URL inválida para libro ".$this->id." ".$datos["url"];
                return false;
            }
        }

        $this->nombre = (isset($datos["nombre"]))?$datos["nombre"]:$this->nombre;
        $this->descripcion = (isset($datos["descripcion"]))?$datos["descripcion"]:$this->descripcion;
        $this->url = (isset($datos["url"]))?$datos["url"]:$this->url;

        //consulta para leer una fila
        $query =    "UPDATE ".$this->tabla." 
                     SET name = ?, 
                         description = ?,
                         url = ?,
                         modified = NOW()
                     WHERE
                        id = ?";

        //preparar consulta
        $conn = DB::get()->conn();
        $stmt = $conn->prepare($query);

        //reemplazar los parámetros de la consulta
        $stmt->bindParam(1, $this->nombre);
        $stmt->bindParam(2, $this->descripcion);
        $stmt->bindParam(3, $this->url);
        $stmt->bindParam(4, $this->id);
        //ejecutar consulta
        return $stmt->execute();
    }

    //crea un objeto en base de datos
    function crear($datos){

        //PDO se encarga de evitar SQL injection via bindParam
        //Solo hace falta validar reglas de dominio
        if(!isset($datos["nombre"]) || strlen(trim($datos["nombre"]))==0){
            $this->error = "Nombre inválido para libro ".$datos["nombre"];
            return false;
        }

        if(!isset($datos["descripcion"]) || strlen(trim($datos["descripcion"]))==0){
            $this->error = "Descripcion inválida para libro ".$datos["descripcion"];
            return false;
        }
        
        $url = filter_var($datos["url"], FILTER_SANITIZE_URL);
        $urlval = filter_var($url, FILTER_VALIDATE_URL);
        if(!isset($datos["url"]) || !$urlval){
            $this->error = "URL inválida para libro ".$url;
            return false;
        }

        $this->nombre = $datos["nombre"];
        $this->descripcion = $datos["descripcion"];
        $this->url = $url;

        //consulta para leer una fila
        $query =    "INSERT INTO ".$this->tabla." 
                        (name, description, url, created, modified)
                     VALUES (?, ?, ?, NOW(), NOW())";

        //preparar consulta
        $conn = DB::get()->conn();
        $stmt = $conn->prepare($query);

        //reemplazar los parámetros de la consulta
        $stmt->bindParam(1, $this->nombre);
        $stmt->bindParam(2, $this->descripcion);
        $stmt->bindParam(3, $this->url);

        //ejecutar consulta
        return $stmt->execute();
    }

    function getDetalle(){
        return [
            "nombre"=>$this->nombre,
            "descripcion"=>$this->descripcion,
            "url"=>$this->url,
            "creada"=>$this->creada,
            "modificada"=>$this->creada
        ];
    }

    function ultimoError(){
        return $this->error;
    }
}