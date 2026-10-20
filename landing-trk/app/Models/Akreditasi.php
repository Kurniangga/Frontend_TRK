<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Akreditasi extends Model
{
    protected $table = 'akreditasi';

    protected $fillable = ['file_path', 'file_name'];

    /** Dokumen terbaru = baris dengan id terbesar. */
    public static function terbaru(): ?self
    {
        return static::query()->latest('id')->first();
    }

    public function getUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->file_path);
    }
}
