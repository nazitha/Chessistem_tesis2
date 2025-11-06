<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Exception;

class ImportExternalData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:external-data 
                            {--source=external : Nombre de la conexión de origen}
                            {--target=default : Nombre de la conexión de destino}
                            {--tables=* : Tablas específicas a migrar (opcional)}
                            {--truncate : Vaciar tablas antes de importar}
                            {--force : Forzar importación sin confirmación}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importa datos desde una base de datos externa (phpMyAdmin) a la base de datos actual';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $sourceConnection = $this->option('source');
        $targetConnection = $this->option('target');
        $specificTables = $this->option('tables');
        $truncate = $this->option('truncate');
        $force = $this->option('force');

        try {
            // Verificar conexiones
            $this->info('Verificando conexiones...');
            $this->checkConnections($sourceConnection, $targetConnection);

            // Obtener lista de tablas
            $tables = $this->getTablesToMigrate($sourceConnection, $specificTables);
            
            if (empty($tables)) {
                $this->error('No se encontraron tablas para migrar.');
                return 1;
            }

            // Mostrar resumen
            $this->showMigrationSummary($tables, $sourceConnection, $targetConnection);

            // Confirmar antes de proceder
            if (!$force && !$this->confirm('¿Deseas continuar con la migración?')) {
                $this->info('Migración cancelada.');
                return 0;
            }

            // Ejecutar migración
            $this->migrateTables($tables, $sourceConnection, $targetConnection, $truncate);

            $this->info('✅ Migración completada exitosamente!');
            return 0;

        } catch (Exception $e) {
            $this->error('❌ Error durante la migración: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Verificar que las conexiones funcionen
     */
    private function checkConnections($sourceConnection, $targetConnection)
    {
        try {
            DB::connection($sourceConnection)->getPdo();
            $this->info("✅ Conexión origen ({$sourceConnection}) verificada");
        } catch (Exception $e) {
            throw new Exception("No se puede conectar a la base de datos origen: " . $e->getMessage());
        }

        try {
            DB::connection($targetConnection)->getPdo();
            $this->info("✅ Conexión destino ({$targetConnection}) verificada");
        } catch (Exception $e) {
            throw new Exception("No se puede conectar a la base de datos destino: " . $e->getMessage());
        }
    }

    /**
     * Obtener lista de tablas a migrar
     */
    private function getTablesToMigrate($sourceConnection, $specificTables)
    {
        if (!empty($specificTables)) {
            return $specificTables;
        }

        // Obtener todas las tablas de la base de datos origen
        $tables = DB::connection($sourceConnection)
            ->select("SHOW TABLES");
        
        $tableNames = [];
        foreach ($tables as $table) {
            $tableArray = (array) $table;
            $tableNames[] = reset($tableArray);
        }

        return $tableNames;
    }

    /**
     * Mostrar resumen de la migración
     */
    private function showMigrationSummary($tables, $sourceConnection, $targetConnection)
    {
        $this->info("\n📋 Resumen de la migración:");
        $this->info("Origen: {$sourceConnection}");
        $this->info("Destino: {$targetConnection}");
        $this->info("Tablas a migrar: " . count($tables));
        
        $this->table(['Tabla', 'Registros'], $this->getTableInfo($tables, $sourceConnection));
    }

    /**
     * Obtener información de las tablas
     */
    private function getTableInfo($tables, $sourceConnection)
    {
        $info = [];
        foreach ($tables as $table) {
            try {
                $count = DB::connection($sourceConnection)
                    ->table($table)
                    ->count();
                $info[] = [$table, $count];
            } catch (Exception $e) {
                $info[] = [$table, 'Error: ' . $e->getMessage()];
            }
        }
        return $info;
    }

    /**
     * Migrar las tablas
     */
    private function migrateTables($tables, $sourceConnection, $targetConnection, $truncate)
    {
        $bar = $this->output->createProgressBar(count($tables));
        $bar->start();

        foreach ($tables as $table) {
            try {
                $this->migrateTable($table, $sourceConnection, $targetConnection, $truncate);
                $bar->advance();
            } catch (Exception $e) {
                $this->error("\n❌ Error migrando tabla {$table}: " . $e->getMessage());
                $bar->advance();
            }
        }

        $bar->finish();
        $this->newLine();
    }

    /**
     * Migrar una tabla específica
     */
    private function migrateTable($table, $sourceConnection, $targetConnection, $truncate)
    {
        // Verificar si la tabla existe en el destino
        if (!Schema::connection($targetConnection)->hasTable($table)) {
            $this->warn("⚠️  La tabla {$table} no existe en el destino. Creando estructura...");
            $this->createTableStructure($table, $sourceConnection, $targetConnection);
        }

        // Vaciar tabla si se solicita
        if ($truncate) {
            DB::connection($targetConnection)->table($table)->truncate();
        }

        // Obtener datos de la tabla origen
        $data = DB::connection($sourceConnection)
            ->table($table)
            ->get()
            ->toArray();

        if (empty($data)) {
            $this->info("ℹ️  La tabla {$table} está vacía, saltando...");
            return;
        }

        // Insertar datos en lotes
        $chunks = array_chunk($data, 1000); // Procesar en lotes de 1000 registros
        
        foreach ($chunks as $chunk) {
            $insertData = [];
            foreach ($chunk as $row) {
                $insertData[] = (array) $row;
            }
            
            DB::connection($targetConnection)
                ->table($table)
                ->insert($insertData);
        }

        $this->info("✅ Tabla {$table} migrada exitosamente (" . count($data) . " registros)");
    }

    /**
     * Crear estructura de tabla en el destino
     */
    private function createTableStructure($table, $sourceConnection, $targetConnection)
    {
        try {
            // Obtener estructura de la tabla origen
            $createTable = DB::connection($sourceConnection)
                ->select("SHOW CREATE TABLE `{$table}`");
            
            $createStatement = $createTable[0]->{'Create Table'};
            
            // Ejecutar CREATE TABLE en el destino
            DB::connection($targetConnection)->statement($createStatement);
            
        } catch (Exception $e) {
            throw new Exception("No se pudo crear la estructura de la tabla {$table}: " . $e->getMessage());
        }
    }
}


