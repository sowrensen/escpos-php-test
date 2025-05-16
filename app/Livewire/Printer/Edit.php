<?php

namespace App\Livewire\Printer;

use App\Models\Printer;
use Livewire\Component;

class Edit extends Component
{
    public Printer $printer;

    public string $title = '';
    public string $type = ''; 
    public ?int $characters_per_line = null;
    public ?string $path = null;
    public ?string $ip_address = null;
    public ?int $port = null;

    public function mount(Printer $printer)
    {
        $this->printer = $printer;
        $this->title = $printer->title;
        $this->type = $printer->type;
        $this->characters_per_line = $printer->characters_per_line;
        $this->path = $printer->path;
        $this->ip_address = $printer->ip_address;
        $this->port = $printer->port;
    }

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
                function ($attribute, $value, $fail) {
                    if (in_array($this->type, ['usb', 'serial', 'windows']) && empty($value)) {
                        $fail('The path field is required when type is USB, Serial, or Windows.');
                    }
                }
            ],
            'ip_address' => [
                'nullable',
                'ip',
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

    public function update()
    {
        $validatedData = $this->validate();

        if ($validatedData['type'] === 'network') {
            $validatedData['path'] = null;
        } else {
            $validatedData['ip_address'] = null;
            $validatedData['port'] = null;
        }

        $this->printer->update($validatedData);

        session()->flash('message', 'Printer updated successfully.');

        return redirect()->route('printers.index');
    }

    public function render()
    {
        return view('livewire.printer.edit');
    }
}
