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
        $user = config('database.connections.mysql.username');
        $pass = config('database.connections.mysql.password');
        $host = config('database.connections.mysql.host');
        $port = config('database.connections.mysql.port', 3306);

        $filename = 'baltbep-backup-' . date('Y-m-d_H-i-s') . '.sql';

        // Try mysqldump first (use absolute path for XAMPP)
        $sql = null;
        $mysqldumpAvailable = false;
        
        // Detect mysqldump path (XAMPP default or system)
        $mysqldumpPath = 'C:\\xampp\\mysql\\bin\\mysqldump.exe';
        if (!file_exists($mysqldumpPath)) {
            // Try to find mysqldump in PATH
            $mysqldumpPath = 'mysqldump';
        }
        
        try {
            $command = [
                $mysqldumpPath,
                '-h', $host,
                '-P', $port,
                '-u', $user,
                '-p' . $pass,
                '--single-transaction',
                '--quick',
                '--lock-tables=false',
                $db
            ];
            $process = new Process($command);
            $process->run();
            if ($process->isSuccessful()) {
                $sql = $process->getOutput();
                $mysqldumpAvailable = true;
            }
        } catch (\Throwable $e) {
            // Fallback to PHP export below
        }

        // Fallback: PHP-based export (structure + data, basic)
        if (!$mysqldumpAvailable) {
            $pdo = \DB::connection('mysql')->getPdo();
            $sql = "-- Simple PHP MySQL dump\n";
            $sql .= "-- Database: `$db`\n\n";
            $tables = [];
            $stmt = $pdo->query("SHOW TABLES");
            while ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
                $tables[] = $row[0];
            }
            foreach ($tables as $table) {
                // Structure
                $row = $pdo->query("SHOW CREATE TABLE `$table`")->fetch(\PDO::FETCH_ASSOC);
                $sql .= "--\n-- Table structure for table `$table`\n--\n\n";
                $sql .= $row['Create Table'] . ";\n\n";
                // Data
                $sql .= "--\n-- Dumping data for table `$table`\n--\n\n";
                $data = $pdo->query("SELECT * FROM `$table`");
                while ($d = $data->fetch(\PDO::FETCH_ASSOC)) {
                    $vals = array_map(function($v) use ($pdo) {
                        if ($v === null) return 'NULL';
                        return $pdo->quote($v);
                    }, array_values($d));
                    $sql .= "INSERT INTO `$table` VALUES (" . implode(",", $vals) . ");\n";
                }
                $sql .= "\n";
            }
        }

        return Response::make($sql, 200, [
            'Content-Type' => 'application/sql',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
