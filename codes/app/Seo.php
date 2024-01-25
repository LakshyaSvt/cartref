<?php

namespace App;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;


class Seo extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'url',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'header_script',
        'footer_script',
        'status',
        'order_id'
    ];

    public function save(array $options = [])
    {

        if (!$this->created_by && Auth::user()) {
            $this->created_by = Auth::user()->id;
        }

        parent::save();

    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id', 'created_by');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}
