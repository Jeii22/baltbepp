<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class DatabaseBackupController extends Controller
{
    /**
     * Download a database backup (SQL dump).
     * Only superadmin can access.
     */
    public function download(Request $request)
    {
        if (!Auth::user() || Auth::user()->role !== 'super_admin') {
            abort(403, 'Unauthorized');
        }

        $db = config('database.connections.mysql.database');
        $filename = 'baltbep-backup-' . date('Y-m-d_H-i-s') . '.sql';

        // PHP-based export (works everywhere, no external dependencies)
        $pdo = \DB::connection('mysql')->getPdo();
        
        $sql = "-- BaltBep Database Backup\n";
        $sql .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
        $sql .= "-- Database: `$db`\n\n";
        $sql .= "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n";
        $sql .= "SET time_zone = \"+00:00\";\n\n";
        
        // Get all tables
        $tables = [];
        $stmt = $pdo->query("SHOW TABLES");
        while ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
            $tables[] = $row[0];
        }
        
        foreach ($tables as $table) {
            // Table structure
            $row = $pdo->query("SHOW CREATE TABLE `$table`")->fetch(\PDO::FETCH_ASSOC);
            $sql .= "--\n-- Table structure for `$table`\n--\n\n";
            $sql .= "DROP TABLE IF EXISTS `$table`;\n";
            $sql .= $row['Create Table'] . ";\n\n";
            
            // Table data
            $sql .= "--\n-- Data for `$table`\n--\n\n";
            $data = $pdo->query("SELECT * FROM `$table`");
            
            while ($d = $data->fetch(\PDO::FETCH_ASSOC)) {
                $columns = array_keys($d);
                $values = array_map(function($v) use ($pdo) {
                    if ($v === null) return 'NULL';
                    return $pdo->quote($v);
                }, array_values($d));
                
                $sql .= "INSERT INTO `$table` (`" . implode("`, `", $columns) . "`) VALUES (" . implode(", ", $values) . ");\n";
            }
            $sql .= "\n";
        }

        return Response::make($sql, 200, [
            'Content-Type' => 'application/sql',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
