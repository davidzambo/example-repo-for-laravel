<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'last_name',
        'middle_name',
        'first_name',
        'email',
        'password',
        'password_created_at',
        'user_type_id',
        'sport_id',
        'club_name',
        'subscription_start',
        'subscription_end',
        'confirmation_code',
        'is_carousel'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function sport(){
      return $this->hasOne('App\Sport', 'id','sport_id');
    }

    public function user_type(){
      return $this->belongsTo('App\User_type');
    }

    public function carrier(){
      return $this->hasMany('App\Carrier')->orderBy('season', 'DESC')->orderBy('league', 'DESC');
    }

    public function lastSeasonCarrier(){
      return $this->hasMany('App\Carrier')->where('season', 'like', '%'.date('Y').'%');
    }

    public function language(){
      return $this->belongsToMany('App\Language');
    }

    public function country(){
      return $this->belongsToMany('App\Country');
    }

    public function profile(){
      return $this->hasOne('App\Profile');
    }

    public function position(){
      return $this->belongsToMany('App\Position');
    }

    public function pictures(){
      return $this->hasMany('App\Picture');
    }

    public function profilePicture(){
      return $this->hasMany('App\Picture', 'user_id', 'id')->where('is_profile', true);
    }

    public function favoriteUsers(){
      return $this->hasMany('App\FavoriteUser');
    }

    public function unreadMessages(){
      return $this->hasMany('App\Message', 'to', 'id')->where('viewed_at', null);
    }

    public function clubExperts(){
      return $this->hasMany('App\ClubExperts', 'user_id', 'id')->orderBy('id', 'ASC');
    }

}
