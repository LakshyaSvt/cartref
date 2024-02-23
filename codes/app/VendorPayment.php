<?php

namespace App;

use App\Models\User;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;


class VendorPayment extends Model
{
    use SoftDeletes;

    protected  $fillable = [
        'vendor_id',
        'billing_date',
        'total',
        'utr_no',
        'status'
    ];
    public function save(array $options = [])
    {

        // If no author has been assigned, assign the current user's id as the author of the post
        if (!$this->created_by && Auth::user()) {
            $this->created_by = Auth::user()->id;
        }


        parent::save();

    }


    public function scopeRoleWise($query)
    {

        // if admin show all data else show only individual data
        if(auth()->user()->hasRole(['admin', 'Client']))
        {
            $query;
        }else{
            $query->where('vendor_id', auth()->user()->id);
        }

        return $query;
    }

    public function user(){
        return $this
                ->belongsTo(User::class,'vendor_id', 'id')
                ->select('id','role_id','name','email','brand_name');
    }
}
