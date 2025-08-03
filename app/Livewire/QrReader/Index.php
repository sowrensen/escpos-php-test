<?php

namespace App\Livewire\QrReader;

use Livewire\Component;
use Livewire\WithFileUploads;
use Zxing\QrReader;
use Illuminate\Support\Facades\Storage;

class Index extends Component
{
    use WithFileUploads;

    public $qrFile;
    public $scanResult = '';
    public $isScanning = false;
    public $error = '';

    protected $rules = [
        'qrFile' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
    ];

    protected $messages = [
        'qrFile.required' => 'Please select a QR code image to scan.',
        'qrFile.image' => 'The file must be an image.',
        'qrFile.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif.',
        'qrFile.max' => 'The image may not be greater than 2MB.',
    ];

    public function scanQrCode()
    {
        $this->validate();

        $this->isScanning = true;
        $this->error = '';
        $this->scanResult = '';

        try {
            // Store the uploaded file temporarily
            $path = $this->qrFile->store('temp', 'public');
            $fullPath = storage_path('app/public/' . $path);

            // Read QR code
            $reader = new QrReader($fullPath);
            $result = $reader->text();

            if ($result) {
                $this->scanResult = $result;
            } else {
                $this->error = 'No QR code found in the image or unable to read the QR code.';
            }

            // Clean up temporary file
            Storage::disk('public')->delete($path);

        } catch (\Exception $e) {
            $this->error = 'Error scanning QR code: ' . $e->getMessage();
        } finally {
            $this->isScanning = false;
        }
    }

    public function clearResult()
    {
        $this->scanResult = '';
        $this->error = '';
        $this->qrFile = null;
    }

    public function render()
    {
        return view('livewire.qr-reader.index');
    }
}
