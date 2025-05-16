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
