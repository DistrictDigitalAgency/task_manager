<?php
namespace App;
use Eloquent;

class UserLocation extends Eloquent {

	protected $fillable = [
							'user_id',
							'location_id',
							'from_date',
							'to_date',
							'description'
						];
	protected $primaryKey = 'id';
	protected $table = 'user_locations';

	public function user()
    {
        return $this->belongsTo('App\User');
    }

	public function location()
    {
        return $this->belongsTo('App\Location');
    }
}
