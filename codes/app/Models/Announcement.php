<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'message',
        'priority',
        'for_all_vendors',
        'is_active'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function getColorAttribute(): string
    {
        if ($this->priority === 'high') {
            return 'danger';
        } else if ($this->priority === 'regular') {
            return 'primary';
        } else {
            return 'success';
        }
    }
}
