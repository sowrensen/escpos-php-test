<?php

namespace App\Services;

use App\Models\Printer as PrinterModel;
use Exception;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Illuminate\Support\Facades\Log;

class PrinterService
{
    /**
     * Prints a receipt to the specified printer.
     *
     * @param PrinterModel $printerConfig The printer configuration model.
     * @param string $receiptText The formatted text to print.
     * @return array ['status' => 'success'|'error', 'message' => string]
     */
    public function printReceipt(PrinterModel $printerConfig, string $receiptText): array
    {
        try {
            $connector = null;
            switch (strtolower($printerConfig->type)) {
                case 'network':
                    if (empty($printerConfig->ip_address) || empty($printerConfig->port)) {
                        return ['status' => 'error', 'message' => 'Network printer IP address or port not configured.'];
                    }
                    // Add a timeout to the NetworkPrintConnector constructor (e.g., 5 seconds)
                    $connector = new NetworkPrintConnector($printerConfig->ip_address, $printerConfig->port, 5);
                    break;
                case 'usb':
                case 'serial': // Combined with USB for FilePrintConnector
                case 'file': // Explicitly for file-based printers like shared printers or /dev/usb/lp0
                    if (empty($printerConfig->path)) {
                        return ['status' => 'error', 'message' => 'Printer path (device file or share name) not configured.'];
                    }
                    $connector = new FilePrintConnector($printerConfig->path);
                    break;
                case 'windows': // For Windows shared printers
                    if (empty($printerConfig->path)) {
                        return ['status' => 'error', 'message' => 'Windows shared printer name/path not configured.'];
                    }
                    $connector = new WindowsPrintConnector($printerConfig->path);
                    break;
                // Add case 'cups' if you plan to use CupsPrintConnector for CUPS printers
                // case 'cups':
                //     if (empty($printerConfig->path)) { // 'path' would store the CUPS printer name
                //         return ['status' => 'error', 'message' => 'CUPS printer name not configured.'];
                //     }
                //     $connector = new \Mike42\Escpos\PrintConnectors\CupsPrintConnector($printerConfig->path);
                //     break;
                default:
                    return ['status' => 'error', 'message' => 'Unsupported printer type: ' . $printerConfig->type];
            }

            $printer = new Printer($connector);

            try {
                $printer->initialize();
                $printer->text($receiptText);
                $printer->feed(3); // Feed a few lines before cutting
                $printer->cut();
            } finally {
                $printer->close();
            }

            return ['status' => 'success', 'message' => 'Receipt successfully sent to printer: ' . $printerConfig->title];

        } catch (Exception $e) {
            Log::error("Printing error for printer '{$printerConfig->title}' (ID: {$printerConfig->id}): " . $e->getMessage() . "\nStack Trace:\n" . $e->getTraceAsString());
            return ['status' => 'error', 'message' => 'Could not print to printer \"' . $printerConfig->title . '\": ' . $e->getMessage()];
        }
    }
}
