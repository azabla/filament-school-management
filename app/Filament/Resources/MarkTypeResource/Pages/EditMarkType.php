<?php

namespace App\Filament\Resources\MarkTypeResource\Pages;

use App\Filament\Resources\MarkTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMarkType extends EditRecord
{
    protected static string $resource = MarkTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
