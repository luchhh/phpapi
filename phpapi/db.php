<?php
namespace Phpapi;
use PDO;

// Singleton para conectar a BD
class DB {
  // Instancia a esta misma clase
  private static $instancia = null;
  private $conn;
  
  private $host;
  private $user;
  private $pass;
  private $name;
   
  // La conexión a la BD se establece en el constructor que es privado
  private function __construct()
  {
    $config = include(PHPAPI_DIR.'/config.php');
    $this->host = $config["db"]["host"];
    $this->user = $config["db"]["user"];
    $this->pass = $config["db"]["pass"];
    $this->name = $config["db"]["name"];

    $this->conn = new PDO(
                        "mysql:host={$this->host};dbname={$this->name}", 
                        $this->user,
                        $this->pass,
                        array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'")
                    );
  }
  
  public static function get()
  {
    if(!self::$instancia)
    {
      self::$instancia = new DB();
    }   
    return self::$instancia;
  }
  
  public function conn()
  {
    return $this->conn;
  }
}