<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDO;
use App\Services\Conexion;

class FederacionController extends Controller
{
    private function getConexion()
    {
        $objeto = new Conexion();
        return $objeto->Conectar();
    }

    public function cargarDatos(Request $request)
    {
        $conexion = $this->getConexion();
        $consulta = "
            SELECT
                acronimo,
                nombre_federacion,
                nombre_pais,
                CASE
                WHEN federaciones.federacion_estado = 0 THEN
                    'Inactivo'
                ELSE
                    'Activo'
                END AS estado
            FROM federaciones
                INNER JOIN paises ON paises.id_pais = federaciones.pais_id
        ";
        $resultado = $conexion->prepare($consulta);
        $resultado->execute();
        $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
        return response()->json($data);
    }

    public function cargarPaises(Request $request)
    {
        $conexion = $this->getConexion();
        $consulta = "
            SELECT 
                id_pais,
                nombre_pais
            FROM paises
            ORDER BY nombre_pais
        ";
        $resultado = $conexion->prepare($consulta);
        $resultado->execute();
        $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
        return response()->json($data);
    }

    public function cargarDatosEditar(Request $request)
    {
        $conexion = $this->getConexion();
        $consulta = "
            SELECT
                acronimo,
                nombre_federacion,
                nombre_pais,
                CASE
                WHEN federaciones.federacion_estado = 0 THEN
                    'Inactivo'
                ELSE
                    'Activo'
                END AS estado
            FROM federaciones
                INNER JOIN paises ON paises.id_pais = federaciones.pais_id
            WHERE acronimo LIKE :search
        ";
        $resultado = $conexion->prepare($consulta);
        $resultado->bindParam(':search', $request->search, PDO::PARAM_STR);
        $resultado->execute();
        $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
        return response()->json($data);
    }

    public function insertarFederacion(Request $request)
    {
        $conexion = $this->getConexion();
        $consulta = "
            INSERT INTO federaciones (acronimo, nombre_federacion, pais_id, federacion_estado)
            VALUES (:acronimo, :federacion, :pais_id, :federacion_estado)
        ";
        $resultado = $conexion->prepare($consulta);
        $resultado->bindParam(':acronimo', $request->acronimo, PDO::PARAM_STR);
        $resultado->bindParam(':federacion', $request->federacion, PDO::PARAM_STR);
        $resultado->bindParam(':pais_id', $request->pais_id, PDO::PARAM_INT);
        $resultado->bindParam(':federacion_estado', $request->federacion_estado, PDO::PARAM_INT);

        if ($resultado->execute()) {
            return response()->json(["success" => true]);
        } else {
            return response()->json(["success" => false, "error" => $resultado->errorInfo()]);
        }
    }

    public function actualizarFederacion(Request $request)
    {
        $conexion = $this->getConexion();
        $consulta = "
            UPDATE federaciones
            SET
                acronimo = :acronimo,
                nombre_federacion = :federacion,
                pais_id = :pais,
                federacion_estado = :estado
            WHERE acronimo = :search
        ";
        $resultado = $conexion->prepare($consulta);
        $resultado->bindParam(':acronimo', $request->acronimo, PDO::PARAM_STR);
        $resultado->bindParam(':federacion', $request->federacion, PDO::PARAM_STR);
        $resultado->bindParam(':pais', $request->pais, PDO::PARAM_INT);
        $resultado->bindParam(':estado', $request->estado, PDO::PARAM_INT);
        $resultado->bindParam(':search', $request->search, PDO::PARAM_STR);

        if ($resultado->execute()) {
            return response()->json(["success" => true]);
        } else {
            return response()->json(["success" => false, "error" => $resultado->errorInfo()]);
        }
    }

    public function eliminarFederacion(Request $request)
    {
        $conexion = $this->getConexion();
        $consulta = "
            DELETE FROM federaciones
            WHERE acronimo = :search
        ";
        $resultado = $conexion->prepare($consulta);
        $resultado->bindParam(':search', $request->search, PDO::PARAM_STR);

        if ($resultado->execute()) {
            return response()->json(["success" => true]);
        } else {
            return response()->json(["success" => false, "error" => $resultado->errorInfo()]);
        }
    }

    public function insertarPais(Request $request)
    {
        $conexion = $this->getConexion();
        $consulta = "
            INSERT INTO paises (nombre_pais) 
            VALUES (:pais_add)
        ";
        $resultado = $conexion->prepare($consulta);
        $resultado->bindParam(':pais_add', $request->pais_add, PDO::PARAM_STR);

        if ($resultado->execute()) {
            return response()->json(["success" => true]);
        } else {
            return response()->json(["success" => false, "error" => $resultado->errorInfo()]);
        }
    }
}