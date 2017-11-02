<?php
namespace Core;

use PDO;
class Database
{
    private static $instance;
    private $config;
    private $db;
    /**
     * Database constructor.
     */
    private function __construct() {
        $this->config = require_once "config.php";
        $this->db = new PDO('mysql:host=' . $this->config['host'] . '; dbname=' . $this->config['name'],
            $this->config['user'], $this->config['pwd']);

    }

    public static function getInstance() {
        if( empty( self::$instance )) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function select($sql) {
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function selectObj($sql) {
        return $this->db->query($sql)->fetchAll(PDO::FETCH_OBJ);
    }

    public function insert($table, array $values) {
        $keys = array_keys($values);
        $columns = implode(", ", $keys);
        $val = ":" . implode(", :", $keys);
        $sql = "INSERT INTO $table ($columns) VALUES ($val)";
        $result = $this->db->prepare($sql);
        $result->execute($values);
    }

}