<?php
namespace Phpapi;

class PaginadorREST {

    //propiedades para paginación
    public static $default_ini=1;
    public static $default_lim=10;
    public static $max_lim=50;
    
    private $_def_ini;
    private $_def_lim;
    private $_max_lim;

    //constructor vacio
    public function __construct($default_ini=NULL, $default_lim=NULL, $max_lim=NULL){
        $this->_def_ini = isset($default_ini)?$default_ini:self::$default_ini;
        //los limites son necesarios para calcular si debe haber o no un next
        $this->_def_lim = isset($default_lim)?$default_lim:self::$default_lim;
        $this->_max_lim = isset($max_lim)?$max_lim:self::$max_lim;
    }

    //devuelve la parte query de la URL actual
    function query(){
        $esta_url = "http://".$_SERVER["HTTP_HOST"]."".$_SERVER["REQUEST_URI"];
        $url = parse_url($esta_url);
        return isset($url["query"])?$url["query"]:"";
    }

    //devuelve la parte query de la URL modifcada para ver la siguiente pagina
    function next(){
        $ini = "ini";
        $ini_str = (isset($_GET[$ini]))?filter_var($_GET[$ini], FILTER_SANITIZE_NUMBER_INT):"";
        $ini_numero = (isset($_GET[$ini]))?filter_var($_GET[$ini], FILTER_SANITIZE_NUMBER_INT):$this->_def_ini;

        $ini_next = ++$ini_numero;
        //pdte calcular si existen elementos realmente en la siguiente pagina
        //necesario nro de filas en tabla

        $url = $this->query();
        $patron = "#".$ini."=(\d+)#";
        $reemplazo = $ini."=".$ini_next;

        if(strlen($url)>0 && strpos($ini."=", $url)!=FALSE){
            $query = preg_replace($patron, $reemplazo, $url);
        }else{
            $query = (strlen($url)==0)?$reemplazo:$url."&".$reemplazo;
        }
        return $query;
    }

    //devuelve la parte query de la URL modifcada para ver la pagina anterior
    function prev(){
        $ini = "ini";
        $ini_str = (isset($_GET[$ini]))?filter_var($_GET[$ini], FILTER_SANITIZE_NUMBER_INT):"";
        $ini_numero = (isset($_GET[$ini]))?filter_var($_GET[$ini], FILTER_SANITIZE_NUMBER_INT):$this->_def_ini;

        $ini_prev = --$ini_numero;
        if($ini_prev<$this->_def_ini) return NULL;

        $url = $this->query();
        $patron = "#".$ini."=(\d+)#";
        $reemplazo = $ini."=".$ini_prev;

        if(strlen($url)>0 && strpos($ini."=", $url)!=FALSE){
            $query = preg_replace($patron, $reemplazo, $url);
        }else{
            $query = (strlen($url)==0)?$reemplazo:$url."&".$reemplazo;
        }
        return $query;
    }
}