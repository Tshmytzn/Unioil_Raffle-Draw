<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\GenerateQr;
use App\Models\QrCode;
use App\Models\QueueingStatusModel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\ExportFilesModel;
class QrCodeController extends Controller
{
    public function generate(Request $req)
    {
        $latestQueue = QueueingStatusModel::latest()->first();
        $queue = new QueueingStatusModel();

        if($latestQueue){
            $queue->queue_number = $latestQueue->queue_number + 1;
        }else{
            $queue->queue_number = 1;
        }

        $queue->items = 0;
        $queue->total_items = $req->numberofqr;
        $queue->status = 'inprogress';
        $queue->entry_type = $req->qrtype;
        $queue->type = 'QR Generation';
        $queue->save();

        for($i = 0; $i < $req->numberofqr; $i++){
            GenerateQr::dispatch($req->qrtype);
        }


        return response()->json(['success' => true]);
    }

    public function getqrcodegenerated()
    {
        $qrcodes = QrCode::all();

        return response()->json(['qrcodes' => $qrcodes]);
    }

    public function deletegeneratedqr(request $request){
        $qrcodes = QrCode::where('qr_id', $request->qr_id)->first();
        if($qrcodes){

            $filePath = public_path('qr-codes/'.$qrcodes->image); // Example file path

            if (file_exists($filePath)) {
                unlink($filePath);
            }

            $qrcodes->delete();
        }
        return response()->json(['success' => true]);
    }

    public function queueProgress(Request $req){
        $queue = QueueingStatusModel::all();
        foreach($queue as $q){
            $export = ExportFilesModel::where('queue_id', $q->queue_id)->first();

            if($export){
                $q->export = $export;
            }else{
                $q->export = null;
            }
        }
        return response()->json(['queue'=> $queue]);
    }

    public function exportQR(Request $req){
        $latestQueue = QueueingStatusModel::latest()->first();
        $queue = new QueueingStatusModel();

        if($latestQueue){
            $queue->queue_number = $latestQueue->queue_number + 1;
        }else{
            $queue->queue_number = 1;
        }

        $queue->items = 0;
        $queue->total_items = $req->page_number;
        $queue->status = 'inprogress';
        $queue->entry_type = $req->qrtype;
        $queue->type = 'PDF Export';
        $queue->save();

        $limit = 36 * $req->page_number;

        $qrCodes = QrCode::where('export_status', 'none')->where('status', 'unused')->take($limit)->select('image', 'qr_id')->get();


        $chunkedQrCodes = $qrCodes->chunk(36)->toArray();

        $pdf = Pdf::loadView('Admin.pdf.export_qr', ['qrCodeChunk'=> $chunkedQrCodes]);

        foreach($chunkedQrCodes as $qrCodesC){
            foreach($qrCodesC as $qrCode){
                $qr = QrCode::where('qr_id', $qrCode['qr_id'])->first();

                $qr->update([
                    'export_status'=> 'exported'
                ]);
            }
        }

        $checkExport = ExportFilesModel::latest()->first();
        $export = new ExportFilesModel();
        if(!$checkExport){
            $fileName = "qr_codes_export_1.pdf";
        }else{
            $inc = $checkExport->exp_id + 1;
            $fileName = "qr_codes_export_$inc.pdf";
        }
        $export->file_name = $fileName;
        $export->queue_id = $queue->queue_id;
        $export->save();


        $pdfFilePath = public_path("pdf_files/$fileName");  // Define the file path in the public folder
        $pdf->save($pdfFilePath);


        return $pdf->stream('invoice.pdf');

    }
}
