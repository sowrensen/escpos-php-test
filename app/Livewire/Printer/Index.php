<?php

namespace App\Livewire\Printer;

use App\Models\Printer;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    protected $queryString = ['search'];

    public function delete(Printer $printer)
    {
        $printer->delete();
        session()->flash('message', 'Printer deleted successfully.');
    }

    public function initiateTestPrint($printerId)
    {
        $printer = Printer::find($printerId);
        if (!$printer) {
            session()->flash('error', 'Printer not found.');
            return;
        }

        $charsPerLine = $printer->characters_per_line ?? 42;
        $receipt = [];
        $receipt[] = $this->centerText("Woodland Restaurant", $charsPerLine);
        $receipt[] = $this->centerText("(Vegeterian)", $charsPerLine);
        $receipt[] = str_repeat('-', $charsPerLine);
        $receipt[] = $this->padSides("GSTTIN : 29AADFW1381M1ZO", "Date: 20/06/2022", $charsPerLine);
        $receipt[] = $this->truncateText("Whatsapp or Call - 9590176333", $charsPerLine);
        $receipt[] = $this->truncateText("FSSAI: 11218328000332", $charsPerLine);
        $receipt[] = $this->truncateText("KOT:438274,438295", $charsPerLine);
        $receipt[] = $this->padSides("Wtr:2 Clk:111", "", $charsPerLine);
        $receipt[] = $this->padSides("Bill No.:38345 Table No.: 10", "Dining 4", $charsPerLine);
        $receipt[] = str_repeat('-', $charsPerLine);
        $receipt[] = $this->formatLine("Particulars", "Qty", "Rate", "Amount", $charsPerLine);
        $receipt[] = str_repeat('-', $charsPerLine);

        $items = [
            ["BANANA BUNS 1", 1, 27.00, 27.00],
            ["COFFEE", 2, 23.00, 46.00],
            ["MOODE (LEAF IDLY)", 1, 65.00, 65.00],
            ["NEER DOSA 3", 1, 70.00, 70.00],
            ["PINEAPPLE SHEERA", 1, 50.00, 50.00],
            ["SHAVIGE", 1, 70.00, 70.00],
        ];

        foreach ($items as $item) {
            $receipt[] = $this->formatLine((string)$item[0], (string)$item[1], number_format($item[2], 2), number_format($item[3], 2), $charsPerLine);
        }
        $receipt[] = str_repeat('-', $charsPerLine);
        $receipt[] = $this->formatLine("Total", "7", "", "328.00", $charsPerLine);
        $receipt[] = str_repeat('-', $charsPerLine);

        $receipt[] = $this->truncateText("* GST @ 5% inclusive", $charsPerLine);
        $receipt[] = $this->truncateText("  CGST @ 2.5% = 7.81", $charsPerLine);
        $receipt[] = $this->truncateText("  SGST @ 2.5% = 7.81", $charsPerLine);
        $receipt[] = "";
        $receipt[] = $this->rightAlign("Rs.328.00", $charsPerLine);
        $receipt[] = $this->truncateText("Grand Total", $charsPerLine);
        $receipt[] = $this->truncateText("SAC (Tariff 996331)", $charsPerLine);
        $receipt[] = $this->truncateText("FSSAI: 11218328000332", $charsPerLine);

        $receiptContent = implode("\n", $receipt);

        $this->dispatch('print-receipt-content', content: $receiptContent);
    }

    private function truncateText(string $text, int $width): string
    {
        return mb_substr($text, 0, $width);
    }

    private function centerText(string $text, int $width): string
    {
        $text = $this->truncateText($text, $width);
        $padding = $width - mb_strlen($text);
        $leftPad = floor($padding / 2);
        $rightPad = ceil($padding / 2);
        return str_repeat(' ', $leftPad) . $text . str_repeat(' ', $rightPad);
    }

    private function padSides(string $leftText, string $rightText, int $width): string
    {
        $leftText = $this->truncateText($leftText, $width -1);
        $rightText = $this->truncateText($rightText, $width - mb_strlen($leftText) -1);
        $space = $width - mb_strlen($leftText) - mb_strlen($rightText);
        if ($space < 1) $space = 1;
        return $leftText . str_repeat(' ', $space) . $rightText;
    }

    private function rightAlign(string $text, int $width): string
    {
        $text = $this->truncateText($text, $width);
        $padding = $width - mb_strlen($text);
        return str_repeat(' ', $padding > 0 ? $padding : 0) . $text;
    }

    private function formatLine(string $particulars, string $qty, string $rate, string $amount, int $width): string
    {
        $qtyWidth = 4;
        $rateWidth = 8;
        $amountWidth = 8;
        $minParticularsWidth = 10;

        $particularsWidth = $width - ($qtyWidth + $rateWidth + $amountWidth + 3);

        if ($particularsWidth < $minParticularsWidth) {
            $particularsWidth = $minParticularsWidth;
        }

        $formattedParticulars = str_pad(mb_substr($particulars, 0, $particularsWidth), $particularsWidth);
        $formattedQty = str_pad($qty, $qtyWidth, ' ', STR_PAD_LEFT);
        $formattedRate = str_pad($rate, $rateWidth, ' ', STR_PAD_LEFT);
        $formattedAmount = str_pad($amount, $amountWidth, ' ', STR_PAD_LEFT);

        $line = $formattedParticulars . ' ' . $formattedQty . ' ' . $formattedRate . ' ' . $formattedAmount;
        if (mb_strlen($line) > $width) {
            $over = mb_strlen($line) - $width;
            $formattedParticulars = str_pad(mb_substr($particulars, 0, $particularsWidth - $over), $particularsWidth - $over);
            $line = $formattedParticulars . ' ' . $formattedQty . ' ' . $formattedRate . ' ' . $formattedAmount;
        }
        return mb_substr($line, 0, $width);
    }

    public function render()
    {
        $printers = Printer::query()
            ->when($this->search, fn($query) =>
                $query->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('type', 'like', '%' . $this->search . '%')
                    ->orWhere('ip_address', 'like', '%' . $this->search . '%')
                    ->orWhere('path', 'like', '%' . $this->search . '%')
            )
            ->paginate(10);

        return view('livewire.printer.index', [
            'printers' => $printers,
        ]);
    }
}
