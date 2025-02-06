<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Card;
use App\Models\Edition;
use App\Models\Set;
use App\Models\Type;
use App\Models\Subtype;
use App\Models\Classe;
use App\Models\CardType;
use App\Models\CardClass;
use App\Models\CardSubtype;
use Carbon\Carbon;

class ImportController extends Controller
{
    public function import()
    {

        $page = 1;
        $hasMore = true;

        while ($hasMore) {
            $response = Http::get('https://api.gatcg.com/cards/search', ['page' => $page]);

            if ($response->failed()) {
                return response()->json(['error' => 'Failed to fetch data from API'], 500);
            }
    
            $response = $response->json();
    
            if (!empty($response['data'])) {
                $data = $response['data'];
    
                foreach ($data as $cardData) {
                    $lastUpdate = Carbon::parse($cardData['last_update'])->toDateTimeString();
    
                    $card = Card::updateOrCreate(
                        ['uuid' => $cardData['uuid']],
                        [
                            'element' => $cardData['element'],
                            'name' => $cardData['name'],
                            'slug' => $cardData['slug'],
                            'effect' => $cardData['effect'],
                            'flavor' => $cardData['flavor'],
                            'cost_memory' => $cardData['cost_memory'],
                            'cost_reserve' => $cardData['cost_reserve'],
                            'level' => $cardData['level'],
                            'power' => $cardData['power'],
                            'life' => $cardData['life'],
                            'durability' => $cardData['durability'],
                            'speed' => $cardData['speed'],
                            'legality' => $cardData['legality'],
                            'last_update' => $lastUpdate,
                        ]
                    );
                    
                    if (!empty($cardData['types'])) {
                        $typeIds = [];
                        foreach ($cardData['types'] as $typeName) {
                            $type = Type::firstOrCreate(['value' => $typeName]);
                            $typeIds[] = $type->id;
                        }
                        $card->types()->syncWithoutDetaching($typeIds);
                    }
    
    
                    if (!empty($cardData['subtypes'])) {
                        $subtypeIds = [];
                        foreach ($cardData['subtypes'] as $subtypeName) {
                            $subtype = Subtype::firstOrCreate(['value' => $subtypeName]);
                            $subtypeIds[] = $subtype->id;
                        }
                        $card->subtypes()->syncWithoutDetaching($subtypeIds);
                    }
    
                    if (!empty($cardData['classes'])) {
                        $classIds = [];
                        foreach ($cardData['classes'] as $className) {
                            $class = Classe::firstOrCreate(['value' => $className]);
                            $classIds[] = $class->id;
                        }
                        $card->classes()->syncWithoutDetaching($classIds);
                    }
    
                    if (!empty($cardData['editions'])) {
                        foreach ($cardData['editions'] as $cardEdition) {
                            $lastUpdate = Carbon::parse($cardEdition['last_update'])->toDateTimeString();
                    
                            if (!empty($cardEdition['set'])) {
                                $editionSet = $cardEdition['set'];
                                $setLastUpdate = Carbon::parse($editionSet['last_update'])->toDateTimeString();
                    
                                $set = Set::firstOrCreate([
                                    'name' => $editionSet['name'],
                                    'prefix' => $editionSet['prefix'],
                                    'language' => $editionSet['language'],
                                    'last_update' => $setLastUpdate,
                                ]);
                            }
    
                            $edition = Edition::updateOrCreate(
                                ['uuid' => $cardEdition['uuid']],
                                [
                                    'card_id' => $card->id,
                                    'card_uuid' => $cardEdition['card_id'],
                                    'collector_number' => $cardEdition['collector_number'],
                                    'slug' => $cardEdition['slug'],
                                    'illustrator' => $cardEdition['illustrator'],
                                    'rarity' => $cardEdition['rarity'],
                                    'flavor' => $cardEdition['flavor'],
                                    'last_update' => $lastUpdate,
                                    'set_id' => $set->id,
                                ]
                            );
                        }
                    }
                }
            }

            $page = $response['page'] + 1;
            $hasMore = $response['has_more'];
        }
    }
}