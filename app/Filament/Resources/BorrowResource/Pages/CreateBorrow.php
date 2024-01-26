<?php

namespace App\Filament\Resources\BorrowResource\Pages;

use App\Enums\BookCopyStatusEnum;
use App\Enums\BorrowStatusEnum;
use App\Filament\Resources\BorrowResource;
use App\Models\BookCopy;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateBorrow extends CreateRecord
{
    protected static string $resource = BorrowResource::class;



    protected function handleRecordCreation(array $data): Model
    {
        $bookCopy =  BookCopy::where('book_id', $data['book_copy_id'])->get();
        // dd($bookCopy);
        $data['book_id'] = $data['book_copy_id'];

        foreach($bookCopy as $copy)
        {
            if ($copy->status == BookCopyStatusEnum::Available)
            {
                $data['book_copy_id'] = $copy->id;
                $copy->status = BookCopyStatusEnum::Unavailable;
                $copy->save();
                return static::getModel()::create($data);
            }
        }

        //Create exception if creation fails

        return static::getModel()::create($data);
    }
}
