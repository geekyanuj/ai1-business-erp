<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\QcPdfService;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use ZipArchive;
use Symfony\Component\HttpFoundation\StreamedResponse;

class QcController extends Controller
{
    public function index()
    {
        return view('qc-check.index');
    }

    public function upload(Request $request, QcPdfService $qcPdfService)
    {
        set_time_limit(300);
        ini_set('memory_limit', '512M');

        DB::beginTransaction();

        $tempBase = storage_path('app/private/qc_temp');
        $finalBase = storage_path('app/qc_output');

        $batchName = 'batch_' . date('Ymd_His');
        $tempBatchDir = $tempBase . '/' . $batchName;
        $tempOutputDir = $tempBatchDir . '/outputs';
        $finalBatchDir = $finalBase . '/' . $batchName;

        try {

            /* ================= VALIDATION ================= */
            $request->validate([
                'excel' => 'required|file|mimes:xlsx,xls',
                'pdfs' => 'required|array|min:1',
                'pdfs.*' => 'file|mimes:pdf|max:10240',
            ]);

            /* ================= TEMP DIR ================= */
            mkdir($tempOutputDir, 0777, true);

            /* ================= MAP PDFs (NO SAVE) ================= */
            $pdfMap = [];
            foreach ($request->file('pdfs') as $pdf) {
                $pdfMap[trim($pdf->getClientOriginalName())] = $pdf->getPathname();
            }

            /* ================= READ EXCEL ================= */
            $rows = Excel::toArray([], $request->file('excel'))[0] ?? [];

            if (count($rows) <= 1) {
                throw new \Exception('Excel has no data rows');
            }

            unset($rows[0]); // header
            $rows = array_values(array_filter($rows, fn($r) => array_filter($r)));

            /* ================= VALIDATE EXCEL ================= */
            $validRows = [];

            foreach ($rows as $i => $row) {
                $rowNo = $i + 2;

                if (count($row) < 4) {
                    throw new \Exception("Row {$rowNo}: Missing columns");
                }

                $filename = trim((string) $row[0]);
                if (!str_ends_with(strtolower($filename), '.pdf')) {
                    $filename .= '.pdf';
                }

                if (!isset($pdfMap[$filename])) {
                    throw new \Exception("Row {$rowNo}: PDF not uploaded ({$filename})");
                }

                $validRows[] = [
                    'filename' => $filename,
                    'serial' => trim((string) $row[1]),
                    'part' => trim((string) $row[2]),
                    'date' => $row[3],
                ];
            }

            /* ================= DB BATCH ================= */
            $batchName = 'batch_' . date('Ymd_His');
            $batchId = DB::table('qc_batches')->insertGetId([
                'batch_name' => $batchName,
                'total' => count($validRows),
                'processed' => 0,
                'status' => 'processing',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            /* ================= PROCESS PDFs ================= */
            foreach ($validRows as $row) {

                $date = is_numeric($row['date'])
                    ? Carbon::createFromTimestamp(
                        ExcelDate::excelToTimestamp($row['date'])
                    )->format('d-m-Y')
                    : trim((string) $row['date']);

                $outputName = preg_replace('/[^A-Za-z0-9_-]/', '_', $row['serial']) . '.pdf';

                $qcPdfService->process(
                    $pdfMap[$row['filename']],
                    [
                        'serial' => $row['serial'],
                        'part' => $row['part'],
                        'date' => $date,
                    ],
                    'qc_temp/' . $batchName . '/outputs/' . $outputName
                );

                DB::table('qc_batches')->where('id', $batchId)->increment('processed');
            }

            /* ================= MOVE TO FINAL ================= */
            if (!is_dir($finalBatchDir)) {
                mkdir($finalBatchDir, 0777, true);
            }

            foreach (glob($tempOutputDir . '/*.pdf') as $file) {
                rename($file, $finalBatchDir . '/' . basename($file));
            }

            DB::table('qc_batches')->where('id', $batchId)->update([
                'status' => 'completed',
                'updated_at' => now(),
            ]);

            DB::commit();

            \File::deleteDirectory($tempBatchDir);

            // return back()->with('success', 'QC completed successfully. Batch: ' . $batchName);
            // return redirect()->route('qc.progress.view', $batchId);
            return redirect()->route('qc.progress.view', $batchId)->with('qc_progress_allowed', true);

        } catch (\Throwable $e) {

            DB::rollBack();

            if (is_dir($tempBatchDir)) {
                \File::deleteDirectory($tempBatchDir);
            }

            Log::error('QC FAILED', ['error' => $e->getMessage()]);

            return back()->withErrors([
                'QC stopped: ' . $e->getMessage()
            ]);
        }
    }




    public function progress($id)
    {
        return response()->json(
            DB::table('qc_batches')->find($id)
        );
    }

    public function progressView($id)
    {
        if (!session()->has('qc_progress_allowed')) {
            return redirect()
                ->route('qc-check.index')
                ->withErrors('Access denied');
        }

        // One-time access only
        session()->forget('qc_progress_allowed');

        return view('qc-check.progress', [
            'batchId' => $id
        ]);
    }



    public function downloadZip($batch)
    {
        $basePath = storage_path('app/qc_output');
        $batchPath = $basePath . '/' . $batch;

        if (!is_dir($batchPath)) {
            abort(404, 'Batch not found');
        }

        $zipName = $batch . '.zip';

        return response()->streamDownload(function () use ($batchPath) {

            $zip = new ZipArchive();
            $tempZip = tempnam(sys_get_temp_dir(), 'qc_zip_');

            if ($zip->open($tempZip, ZipArchive::CREATE) !== true) {
                throw new \Exception('Cannot create ZIP');
            }

            foreach (glob($batchPath . '/*.pdf') as $file) {
                $zip->addFile($file, basename($file));
            }

            $zip->close();

            readfile($tempZip);
            unlink($tempZip);

        }, $zipName);
    }
}
