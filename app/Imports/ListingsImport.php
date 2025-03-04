<?php

namespace App\Imports;

use App\Models\Listing;
use App\Models\Edition;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\StringValueBinder;
use Flux\Flux;

class ListingsImport extends StringValueBinder implements ToModel, WithHeadingRow, WithCustomValueBinder
{
    protected $userId;

    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    public function model(array $row)
    {

        dd($row['set_refix']);
        if (empty($row['set_refix'])) {
            Flux::toast(variant: 'danger', text: "No row with header Set prefix");
            return null;
        }

        $collectorNumber = str_pad((string) $row['collector_number'], 3, '0', STR_PAD_LEFT);

        $edition = Edition::whereHas('set', function ($query) use ($row) {
            $query->where('prefix', $row['set_prefix']);
        })->where('collector_number', $collectorNumber)->first();


        if (!$edition) {
            Flux::toast(variant: 'danger', text: "Edition not found for Set Prefix:" . $row['set_prefix'] . ", Collector Number: " . $collectorNumber . "!");
            return null;
        }

        $foil = false;

        if ($row['foil'] == 1) {
            $foil = true;
        }

        $listing = Listing::where('user_id', $this->userId)
            ->where('edition_id', $edition->id)
            ->where('price', (float) $row['price'])
            ->where('is_foil', $foil)
            ->first();

        if ($listing) {
            $listing->card_count += (int) $row['amount'];
            $listing->save();
        } else {
            Listing::create([
                'user_id' => $this->userId,
                'edition_id' => $edition->id,
                'card_count' => (int) $row['amount'],
                'is_foil' => (int) $foil,
                'price' => (float) $row['price'],
            ]);
        }

        Flux::toast(variant: 'success', text: 'Listings imported!');
    }
}
