<?php

namespace App\Http\Controllers;

use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Illuminate\Http\Request;

class PrintController extends Controller
{
    public function printReceipt(Request $request)
    {
        try {
            $receiptData = $request->all();

            // Use $receiptData to generate the receipt or perform other operations
            // Create a Windows print connector (you might need a different connector based on your setup)
            $connector = new NetworkPrintConnector("192.168.103.23", 9100);

            // Create a new printer instance
            $printer = new Printer($connector);

            // Print the receipt
            $printer->text("Invoice\n");
            $printer->text("Customer Name: " . $receiptData['customerName'] . "\n");
            $printer->text("-----------------------------\n");

            foreach ($receiptData['items'] as $item) {
                $printer->text($item['name'] . " - " . $item['quantity'] . " pcs - " . $item['price'] . "\n");
            }

            $printer->text("-----------------------------\n");
            $printer->text("Total Price: " . $receiptData['totalPrice'] . "\n");

            $printer->cut();

            // Close the printer
            $printer->close();

            return response()->json([
                'success' => true,
                'data' => $receiptData,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
