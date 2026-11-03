<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Backup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class BackupController extends Controller
{
    public function index()
    {
        $backups = Backup::latest()->paginate(10);
        return view('admin.backups.index', compact('backups'));
    }

    public function store()
    {
        try {
            $filename = 'pustakara-backup-' . date('Y-m-d-His') . '.sql';
            $path = 'backups/' . $filename;

            // Backup sederhana: dump semua tabel sebagai SQL
            $sql = $this->generateSqlDump();

            Storage::disk('local')->put($path, $sql);

            Backup::create([
                'nama_file' => $filename,
                'ukuran'    => $this->formatSize(strlen($sql)),
                'file_path' => $path,
                'status'    => 'sukses',
            ]);

            return redirect()->route('admin.backups.index')
                ->with('success', "Backup berhasil dibuat: {$filename}");

        } catch (\Exception $e) {
            Backup::create([
                'nama_file' => 'gagal-' . date('Ymd-His'),
                'ukuran'    => '0 B',
                'file_path' => '-',
                'status'    => 'gagal',
            ]);

            return redirect()->route('admin.backups.index')
                ->with('error', 'Backup gagal: ' . $e->getMessage());
        }
    }

    public function download(Backup $backup)
    {
        if (!Storage::disk('local')->exists($backup->file_path)) {
            return back()->with('error', 'File backup tidak ditemukan.');
        }

        return Storage::disk('local')->download($backup->file_path);
    }

    public function destroy(Backup $backup)
    {
        if (Storage::disk('local')->exists($backup->file_path)) {
            Storage::disk('local')->delete($backup->file_path);
        }

        $backup->delete();

        return redirect()->route('admin.backups.index')
            ->with('success', 'File backup berhasil dihapus.');
    }

    // ============ HELPER ============

    private function generateSqlDump(): string
    {
        $tables = DB::select('SHOW TABLES');
        $dbName = config('database.connections.mysql.database');
        $key = 'Tables_in_' . $dbName;

        $sql = "-- Pustakara Database Backup\n";
        $sql .= "-- Tanggal: " . now()->format('Y-m-d H:i:s') . "\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        foreach ($tables as $table) {
            $tableName = $table->$key;

            // Drop table
            $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";

            // Create table
            $create = DB::select("SHOW CREATE TABLE `{$tableName}`");
            $sql .= $create[0]->{'Create Table'} . ";\n\n";

            // Insert data
            $rows = DB::table($tableName)->get();
            if ($rows->count() > 0) {
                $sql .= "INSERT INTO `{$tableName}` VALUES\n";
                $inserts = [];
                foreach ($rows as $row) {
                    $values = array_map(function ($v) {
                        if (is_null($v)) return 'NULL';
                        return "'" . addslashes($v) . "'";
                    }, (array) $row);
                    $inserts[] = '(' . implode(',', $values) . ')';
                }
                $sql .= implode(",\n", $inserts) . ";\n\n";
            }
        }

        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";
        return $sql;
    }

    private function formatSize(int $bytes): string
    {
        if ($bytes < 1024) return $bytes . ' B';
        if ($bytes < 1048576) return round($bytes / 1024, 2) . ' KB';
        return round($bytes / 1048576, 2) . ' MB';
    }
}