<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDO;

class AcademiaController extends Controller
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
                nombre_academia,
                correo_academia,
                telefono_academia,
                representante_academia,
                direccion_academia,
                CONCAT(nombre_ciudad, ', ', IFNULL(nombre_depto, '-'), ' (', IFNULL(nombre_pais, '-'), ')') AS ciudad_id,
                CASE 
                    WHEN estado_academia = 0 THEN 'Inactivo'
                    WHEN estado_academia = 1 THEN 'Activo'
                END AS estado_academia
            FROM academias
                LEFT JOIN ciudades ON ciudades.id_ciudad = academias.ciudad_id
                LEFT JOIN departamentos ON departamentos.id_depto = ciudades.depto_id
                LEFT JOIN paises ON paises.id_pais = departamentos.pais_id
            ORDER BY nombre_academia
        ";
        $resultado = $conexion->prepare($consulta);
        $resultado->execute();
        $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
        return response()->json($data);
    }

    public function cargarCiudades(Request $request)
    {
        $conexion = $this->getConexion();
        $consulta = "
            SELECT
                id_ciudad,
                nombre_ciudad,
                id_depto,
                nombre_depto,
                id_pais,
                nombre_pais,
                CONCAT(nombre_ciudad, ', ', IFNULL(nombre_depto, '-'), ' (', IFNULL(nombre_pais, '-'), ')') AS opc
            FROM ciudades
            LEFT JOIN departamentos ON ciudades.depto_id = departamentos.id_depto
            LEFT JOIN paises ON departamentos.pais_id = paises.id_pais
            ORDER BY opc
        ";
        $resultado = $conexion->prepare($consulta);
        $resultado->execute();
        $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
        return response()->json($data);
    }

    public function store(Request $request)
    {
        $conexion = $this->getConexion();
        $consulta = "
            INSERT INTO academias 
                (nombre_academia, correo_academia, telefono_academia, representante_academia, direccion_academia, ciudad_id, estado_academia) 
            VALUES 
                (:academia, :correo, :telefono, :director, :direccion, :ciudadValue, :estado)
        ";
        $resultado = $conexion->prepare($consulta);
        $resultado->bindParam(':academia', $request->academia, PDO::PARAM_STR);
        $resultado->bindParam(':correo', $request->correo, PDO::PARAM_STR);
        $resultado->bindParam(':telefono', $request->telefono, PDO::PARAM_INT);
        $resultado->bindParam(':director', $request->director, PDO::PARAM_STR);
        $resultado->bindParam(':direccion', $request->direccion, PDO::PARAM_STR);
        $resultado->bindParam(':ciudadValue', $request->ciudadValue, PDO::PARAM_INT);
        $resultado->bindParam(':estado', $request->estado, PDO::PARAM_BOOL);

        if ($resultado->execute()) {
            return response()->json(["success" => true]);
        } else {
            return response()->json(["success" => false, "error" => $resultado->errorInfo()]);
        }
    }

    public function update(Request $request)
    {
        $conexion = $this->getConexion();
        $consulta = "
            UPDATE academias
            SET
                nombre_academia = :academia,
                correo_academia = :correo,
                telefono_academia = :telefono,
                representante_academia = :director,
                direccion_academia = :direccion,
                ciudad_id = :ciudadValue,
                estado_academia = :estado
            WHERE
                nombre_academia = :search
        ";
        $resultado = $conexion->prepare($consulta);
        $resultado->bindParam(':academia', $request->academia, PDO::PARAM_STR);
        $resultado->bindParam(':correo', $request->correo, PDO::PARAM_STR);
        $resultado->bindParam(':telefono', $request->telefono, PDO::PARAM_INT);
        $resultado->bindParam(':director', $request->director, PDO::PARAM_STR);
        $resultado->bindParam(':direccion', $request->direccion, PDO::PARAM_STR);
        $resultado->bindParam(':ciudadValue', $request->ciudadValue, PDO::PARAM_INT);
        $resultado->bindParam(':estado', $request->estado, PDO::PARAM_BOOL);
        $resultado->bindParam(':search', $request->search, PDO::PARAM_STR);

        if ($resultado->execute()) {
            return response()->json(["success" => true]);
        } else {
            return response()->json(["success" => false, "error" => $resultado->errorInfo()]);
        }
    }

    public function destroy(Request $request)
    {
        $conexion = $this->getConexion();
        $consulta = "
            DELETE FROM academias 
            WHERE nombre_academia = :search
        ";
        $resultado = $conexion->prepare($consulta);
        $resultado->bindParam(':search', $request->search, PDO::PARAM_STR);

        if ($resultado->execute()) {
            return response()->json(["success" => true]);
        } else {
            return response()->json(["success" => false, "error" => $resultado->errorInfo()]);
        }
    }
}