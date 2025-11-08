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

        // Use mysqldump (must be in PATH)
        $command = [
            'mysqldump',
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

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $sql = $process->getOutput();
        return Response::make($sql, 200, [
            'Content-Type' => 'application/sql',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
