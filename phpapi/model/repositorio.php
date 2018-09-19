<?php
namespace Phpapi\Model;

use Phpapi\DB;
use Phpapi\PaginadorREST;
use PDO;

class Repositorio{
 
    //propiedades para paginación
    public $default_ini;
    public $default_lim;
    public $max_lim;

    //nombre de la tabla en BD
    private $tabla = "libro"; 
    private $error;

    //constructor vacio
    public function __construct(){
        //$this->default_ini = 1;
        $this->default_ini = PaginadorREST::$default_ini;
        $this->default_lim = PaginadorREST::$default_lim;
        $this->max_lim = PaginadorREST::$max_lim;
    }

    //buscar un objeto
    function buscar($config=NULL){

        $order_by = "";
        if(isset($config["ord"])){
            $config_ordenes = explode(",",$config["ord"]);
            $columnas_validas = array("nombre"=>"name","descripcion"=>"description","url"=>"url","creada"=>"created","modificada"=>"modified");
            foreach($config_ordenes as $directiva){
                $op = substr($directiva, 0, 1);
                if($op=="-"){
                    $columna = substr($directiva, 1, strlen($directiva)-1);
                    $op = "DESC";
                }else{
                    $columna = $directiva;
                    $op = "ASC";
                }
                if(array_key_exists($columna, $columnas_validas)){
                    $separador = (strlen($order_by)==0)? "ORDER BY ":",";
                    $order_by = $order_by.$separador." ".$columnas_validas[$columna]." ".$op;
                }
            }
        }
        $pag_ini = (isset($config["ini"]))?filter_var($config["ini"], FILTER_SANITIZE_NUMBER_INT):$this->default_ini;
        $pag_paso = (isset($config["lim"]) && $config["lim"]<$this->max_lim)?filter_var($config["lim"], FILTER_SANITIZE_NUMBER_INT):$this->default_lim;
        $q = (isset($config["q"]))?filter_var($config["q"], FILTER_SANITIZE_STRING):"";
        $q = "%".$q."%";

        $limit_ini = ($pag_ini-1)*$pag_paso;
        //pdte filtering

        //consulta para leer varias fila
        $query =    "SELECT p.id, p.name
                     FROM ".$this->tabla." p
                     WHERE p.name LIKE ?
                     OR p.description LIKE ?
                     ".$order_by." 
                     LIMIT
                        ?, ?";

        //preparar consulta
        $conn = DB::get()->conn();
        //$conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);        
        $stmt = $conn->prepare($query);

        //reemplazar los parámetros de la consulta
        $stmt->bindParam(1, $q);
        $stmt->bindParam(2, $q);
        $stmt->bindValue(3, (int)$limit_ini, PDO::PARAM_INT);
        $stmt->bindValue(4, (int)$pag_paso, PDO::PARAM_INT);
        //ejecutar consulta
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function ultimoError(){
        return $this->error;
    }
}