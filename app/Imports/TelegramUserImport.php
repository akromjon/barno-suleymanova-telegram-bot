<?php

namespace App\Imports;

use App\Models\TelegramUser;
use GuzzleHttp\Psr7\UploadedFile;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class TelegramUserImport implements ToCollection, WithChunkReading
{
    public function collection(Collection $collections): void
    {
        $collections = $collections->skip(1);

        $collections->each(callback: function ($c, $k): void {

            TelegramUser::updateOrCreate(attributes: ['chat_id' => $c[0]], values: [
                'first_name' => $c[1],
                'last_name' => $c[2],
                'username' => $c[4]!==null ? '@' . $c[4] : '',
                'subscribed' => $c[5] === '+' ? true : false,
                'created_at' => $c[6],
                'tags'=>$c[10]
            ]);

        });
    }

    public function chunkSize(): int
    {
        return 100;
    }
}
