<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDO;
use App\Services\Conexion;

class CiudadController extends Controller
{
    private function getConexion()
    {
        $objeto = new Conexion();
        return $objeto->Conectar();
    }

    public function index(Request $request)
    {
        $conexion = $this->getConexion();
        $consulta = "
            SELECT 
                p.nombre_pais,
                IFNULL(d.nombre_depto, '-') AS nombre_depto,
                IFNULL(c.nombre_ciudad, '-') AS nombre_ciudad
            FROM 
                paises p
            LEFT JOIN 
                departamentos d ON p.id_pais = d.pais_id
            LEFT JOIN 
                ciudades c ON d.id_depto = c.depto_id
            ORDER BY 
                p.nombre_pais, d.nombre_depto, c.nombre_ciudad;
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

    public function cargarDepartamentos(Request $request)
    {
        $conexion = $this->getConexion();
        $consulta = "
            SELECT
                id_depto,
                nombre_depto
            FROM departamentos
            WHERE pais_id = :pais_id
            ORDER BY nombre_depto
        ";
        $resultado = $conexion->prepare($consulta);
        $resultado->bindParam(':pais_id', $request->pais_id, PDO::PARAM_INT);
        $resultado->execute();
        $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
        return response()->json($data);
    }

    public function storePais(Request $request)
    {
        $conexion = $this->getConexion();
        $consulta = "
            INSERT INTO paises (nombre_pais) 
            VALUES (:nombre_pais)
        ";
        $resultado = $conexion->prepare($consulta);
        $resultado->bindParam(':nombre_pais', $request->nombre_pais, PDO::PARAM_STR);

        if ($resultado->execute()) {
            return response()->json(["success" => true]);
        } else {
            return response()->json(["success" => false, "error" => $resultado->errorInfo()]);
        }
    }

    public function storeDepartamento(Request $request)
    {
        $conexion = $this->getConexion();
        $consulta = "
            INSERT INTO departamentos (pais_id, nombre_depto) 
            VALUES (:pais_id, :nombre_depto)
        ";
        $resultado = $conexion->prepare($consulta);
        $resultado->bindParam(':pais_id', $request->pais_id, PDO::PARAM_INT);
        $resultado->bindParam(':nombre_depto', $request->nombre_depto, PDO::PARAM_STR);

        if ($resultado->execute()) {
            return response()->json(["success" => true]);
        } else {
            return response()->json(["success" => false, "error" => $resultado->errorInfo()]);
        }
    }

    public function storeCiudad(Request $request)
    {
        $conexion = $this->getConexion();
        $consulta = "
            INSERT INTO ciudades (depto_id, nombre_ciudad) 
            VALUES (:depto_id, :nombre_ciudad)
        ";
        $resultado = $conexion->prepare($consulta);
        $resultado->bindParam(':depto_id', $request->depto_id, PDO::PARAM_INT);
        $resultado->bindParam(':nombre_ciudad', $request->nombre_ciudad, PDO::PARAM_STR);

        if ($resultado->execute()) {
            return response()->json(["success" => true]);
        } else {
            return response()->json(["success" => false, "error" => $resultado->errorInfo()]);
        }
    }

    public function updatePais(Request $request)
    {
        $conexion = $this->getConexion();
        $consulta = "
            UPDATE paises
            SET nombre_pais = :nombre_pais
            WHERE nombre_pais = :nombre_pais_anterior
        ";
        $resultado = $conexion->prepare($consulta);
        $resultado->bindParam(':nombre_pais', $request->nombre_pais, PDO::PARAM_STR);
        $resultado->bindParam(':nombre_pais_anterior', $request->nombre_pais_anterior, PDO::PARAM_STR);

        if ($resultado->execute()) {
            return response()->json(["success" => true]);
        } else {
            return response()->json(["success" => false, "error" => $resultado->errorInfo()]);
        }
    }

    public function updateDepartamento(Request $request)
    {
        $conexion = $this->getConexion();
        $consulta = "
            UPDATE departamentos
            SET pais_id = :pais_id, nombre_depto = :nombre_depto
            WHERE pais_id = (
                SELECT id_pais
                FROM paises
                WHERE nombre_pais = :nombre_pais_anterior
            )
            AND nombre_depto = :nombre_depto_anterior
        ";
        $resultado = $conexion->prepare($consulta);
        $resultado->bindParam(':pais_id', $request->pais_id, PDO::PARAM_INT);
        $resultado->bindParam(':nombre_depto', $request->nombre_depto, PDO::PARAM_STR);
        $resultado->bindParam(':nombre_pais_anterior', $request->nombre_pais_anterior, PDO::PARAM_STR);
        $resultado->bindParam(':nombre_depto_anterior', $request->nombre_depto_anterior, PDO::PARAM_STR);

        if ($resultado->execute()) {
            return response()->json(["success" => true]);
        } else {
            return response()->json(["success" => false, "error" => $resultado->errorInfo()]);
        }
    }

    public function updateCiudad(Request $request)
    {
        $conexion = $this->getConexion();
        $consulta = "
            UPDATE ciudades
            SET depto_id = :depto_id, nombre_ciudad = :nombre_ciudad
            WHERE depto_id = (
                SELECT id_depto
                FROM departamentos
                WHERE nombre_depto = :nombre_depto_anterior AND
                pais_id = (
                    SELECT id_pais
                    FROM paises
                    WHERE nombre_pais = :nombre_pais_anterior
                )
            )
            AND nombre_ciudad = :nombre_ciudad_anterior
        ";
        $resultado = $conexion->prepare($consulta);
        $resultado->bindParam(':depto_id', $request->depto_id, PDO::PARAM_INT);
        $resultado->bindParam(':nombre_ciudad', $request->nombre_ciudad, PDO::PARAM_STR);
        $resultado->bindParam(':nombre_depto_anterior', $request->nombre_depto_anterior, PDO::PARAM_STR);
        $resultado->bindParam(':nombre_pais_anterior', $request->nombre_pais_anterior, PDO::PARAM_STR);
        $resultado->bindParam(':nombre_ciudad_anterior', $request->nombre_ciudad_anterior, PDO::PARAM_STR);

        if ($resultado->execute()) {
            return response()->json(["success" => true]);
        } else {
            return response()->json(["success" => false, "error" => $resultado->errorInfo()]);
        }
    }

    public function destroyPais(Request $request)
    {
        $conexion = $this->getConexion();
        $consulta = "
            DELETE FROM paises
            WHERE nombre_pais = :nombre_pais
        ";
        $resultado = $conexion->prepare($consulta);
        $resultado->bindParam(':nombre_pais', $request->nombre_pais, PDO::PARAM_STR);

        if ($resultado->execute()) {
            return response()->json(["success" => true]);
        } else {
            return response()->json(["success" => false, "error" => $resultado->errorInfo()]);
        }
    }

    public function destroyDepartamento(Request $request)
    {
        $conexion = $this->getConexion();
        $consulta = "
            DELETE FROM departamentos
            WHERE pais_id = (
                SELECT id_pais
                FROM paises
                WHERE nombre_pais = :nombre_pais
            )
            AND nombre_depto = :nombre_depto
        ";
        $resultado = $conexion->prepare($consulta);
        $resultado->bindParam(':nombre_pais', $request->nombre_pais, PDO::PARAM_STR);
        $resultado->bindParam(':nombre_depto', $request->nombre_depto, PDO::PARAM_STR);

        if ($resultado->execute()) {
            return response()->json(["success" => true]);
        } else {
            return response()->json(["success" => false, "error" => $resultado->errorInfo()]);
        }
    }

    public function destroyCiudad(Request $request)
    {
        $conexion = $this->getConexion();
        $consulta = "
            DELETE FROM ciudades
            WHERE depto_id = (
                SELECT id_depto
                FROM departamentos
                WHERE nombre_depto = :nombre_depto
                AND pais_id = (
                    SELECT id_pais
                    FROM paises
                    WHERE nombre_pais = :nombre_pais
                )
            )
            AND nombre_ciudad = :nombre_ciudad
        ";
        $resultado = $conexion->prepare($consulta);
        $resultado->bindParam(':nombre_depto', $request->nombre_depto, PDO::PARAM_STR);
        $resultado->bindParam(':nombre_pais', $request->nombre_pais, PDO::PARAM_STR);
        $resultado->bindParam(':nombre_ciudad', $request->nombre_ciudad, PDO::PARAM_STR);

        if ($resultado->execute()) {
            return response()->json(["success" => true]);
        } else {
            return response()->json(["success" => false, "error" => $resultado->errorInfo()]);
        }
    }
}