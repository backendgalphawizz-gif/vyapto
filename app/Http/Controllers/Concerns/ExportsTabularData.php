<?php

/**
 * COPY THIS FILE TO: app/Http/Controllers/Concerns/ExportsTabularData.php
 *
 * Do not leave this copy in the project root — Laravel PSR-4 autoload only loads
 * classes under app/ by namespace. The real file must live at the path above.
 *
 * This file is a duplicate of app/Http/Controllers/Concerns/ExportsTabularData.php
 * for easy copy-paste from the temp folder.
 */

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

trait ExportsTabularData
{
    protected function exportFormat(Request $request): string
    {
        $f = strtolower((string) $request->query('format', 'pdf'));
        if ($f === 'excel' || $f === 'xls') {
            return 'xlsx';
        }
        if (in_array($f, ['csv', 'xlsx'], true)) {
            return $f;
        }

        return 'pdf';
    }

    /**
     * @param  array<int, string>  $headers
     * @param  array<int, array<int, mixed>>  $rows
     */
    protected function streamCsvDownload(string $basename, array $headers, array $rows): StreamedResponse
    {
        $filename = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $basename);
        if (! Str::endsWith(strtolower($filename), '.csv')) {
            $filename .= '.csv';
        }

        return response()->streamDownload(function () use ($headers, $rows) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($out, $headers);
            foreach ($rows as $row) {
                $line = array_map(static function ($v) {
                    if ($v === null) {
                        return '';
                    }
                    if (is_scalar($v) || $v instanceof \Stringable) {
                        return (string) $v;
                    }

                    return json_encode($v);
                }, array_values($row));
                fputcsv($out, $line);
            }
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * @param  array<int, string>  $headers
     * @param  array<int, array<int, mixed>>  $rows
     */
    protected function streamExcelTableDownload(string $basename, string $title, array $headers, array $rows)
    {
        $filename = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $basename);
        if (! preg_match('/\.(xls|html)$/i', $filename)) {
            $filename .= '.xls';
        }

        $html = '<html xmlns:x="urn:schemas-microsoft-com:office:excel"><head><meta charset="utf-8"></head><body><table border="1">';
        $html .= '<tr><th colspan="' . count($headers) . '">' . e($title) . ' — ' . e(now()->format('d M Y H:i')) . '</th></tr><tr>';
        foreach ($headers as $h) {
            $html .= '<th>' . e($h) . '</th>';
        }
        $html .= '</tr>';
        foreach ($rows as $row) {
            $html .= '<tr>';
            foreach (array_values($row) as $cell) {
                $html .= '<td>' . e((string) $cell) . '</td>';
            }
            $html .= '</tr>';
        }
        $html .= '</table></body></html>';

        return response($html, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
