<?php
namespace App;
use Eloquent;

class Profile extends Eloquent
{

    protected $fillable = [
        'first_name',
        'last_name',
        'date_of_birth',
        'work_phone',
        'work_phone_extension',
        'mobile',
        'home',
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'country_id',
        'zipcode',
        'facebook',
        'twitter',
        'google_plus'
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function designation()
    {
        return $this->belongsTo('App\Designation');
    }
}
