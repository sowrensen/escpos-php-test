<?php

namespace App\Livewire\Printer;

use App\Models\Printer;
use Livewire\Component;

class Create extends Component
{
    public string $title = '';
    public string $type = 'network'; // Default type
    public ?int $characters_per_line = 42;
    public ?string $path = null;
    public ?string $ip_address = null;
    public ?int $port = 9100;

    protected function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:network,usb,serial,windows,test'],
            'characters_per_line' => ['nullable', 'integer', 'min:1'],
            'path' => [
                'nullable',
                'string',
                'max:255',
                // Required if type is usb, serial, or windows
                function ($attribute, $value, $fail) {
                    if (in_array($this->type, ['usb', 'serial', 'windows']) && empty($value)) {
                        $fail('The path field is required when type is USB, Serial, or Windows.');
                    }
                }
            ],
            'ip_address' => [
                'nullable',
                'ip',
                // Required if type is network
                function ($attribute, $value, $fail) {
                    if ($this->type === 'network' && empty($value)) {
                        $fail('The IP address field is required when type is Network.');
                    }
                }
            ],
            'port' => [
                'nullable',
                'integer',
                'min:1',
                'max:65535',
                 // Required if type is network
                function ($attribute, $value, $fail) {
                    if ($this->type === 'network' && empty($value)) {
                        $fail('The port field is required when type is Network.');
                    }
                }
            ],
        ];
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function save()
    {
        $validatedData = $this->validate();

        // Ensure path is null if type is network, and ip/port are null otherwise
        if ($validatedData['type'] === 'network') {
            $validatedData['path'] = null;
        } else {
            $validatedData['ip_address'] = null;
            $validatedData['port'] = null;
        }

        Printer::create($validatedData);

        session()->flash('message', 'Printer created successfully.');

        return redirect()->route('printers.index');
    }

    public function render()
    {
        return view('livewire.printer.create');
    }
}
