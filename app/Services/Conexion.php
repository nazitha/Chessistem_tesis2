<?php

namespace App\Services;

use PDO;
use PDOException;

class Conexion
{
    private $host = "localhost";
    private $dbname = "chessystem2";
    private $username = "root";
    private $password = "Nazatest12*";
    private $conexion;

    public function Conectar()
    {
        try {
            $this->conexion = new PDO(
                "mysql:host={$this->host};dbname={$this->dbname};charset=utf8",
                $this->username,
                $this->password
            );
            $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $this->conexion;
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }
} 