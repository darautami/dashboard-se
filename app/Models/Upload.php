<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Upload extends Model
{
    protected $fillable = [
        'upload_date',
        'original_filename',
        'total_rows',
    ];

    protected $casts = [
        'upload_date' => 'date',
    ];

    public function snapshots(): HasMany
    {
        return $this->hasMany(AssignmentSnapshot::class);
    }
}