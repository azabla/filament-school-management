<?php

namespace App\Filament\Resources\MarkTypeResource\Pages;

use App\Filament\Resources\MarkTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMarkTypes extends ListRecords
{
    protected static string $resource = MarkTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
