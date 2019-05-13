<?php
namespace App;
use Eloquent;

class Announcement extends Eloquent {

	protected $fillable = [
							'from_date',
							'to_date',
							'title',
							'description',
							'user_id'
						];
	protected $primaryKey = 'id';
	protected $table = 'announcements';

	public function designation()
    {
        return $this->belongsToMany('App\Designation','announcement_designation','announcement_id','designation_id');
    }

    public function user()
    {
        return $this->belongsTo('App\User','user_id'); 
    }

}
