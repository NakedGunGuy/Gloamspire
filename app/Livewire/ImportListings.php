<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Listing;
use App\Imports\ListingsImport;

class ImportListings extends Component
{
    use WithFileUploads;

    public $file;

    public function import()
    {
        $this->validate([
            'file' => 'required|mimes:xlsx,csv'
        ]);

        Excel::import(new ListingsImport(auth()->id()), $this->file);

        $this->file = null;

        
        $this->modal('import-cards')->close();
    }

    public function render()
    {
        return view('livewire.import-listings');
    }
}

