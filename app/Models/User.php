<?php

namespace App\Models;

use Storage;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Intervention\Image\ImageManager as Image;

class User extends Authenticatable {

    const COVER_FOLDER = 'user_avatars';
    const ACTIVE = 1;
    const BAN = 0;

    use Notifiable,
        HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'nickname',
        'dt_birthday',
        'favorite_team',
        'push_notification',
        'show_profile',
	
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'email',
        'password',
        'push_notification',
        'remember_token',
        'updated_at',
        'show_profile',
        'status'
    ];

    public function saveCoverByUrl($url) {
        $filename = basename($url);
        $ext = pathinfo($url, PATHINFO_EXTENSION);
        $cover = ($ext) ? $filename : md5(uniqid(time(), true)) . '.jpg';
        $this->cover = $cover;
        $manager = new Image();
        $manager->make($url)->save(storage_path('app/public/' . $this->getRealStorageCoverPath($cover)));
    }

    public function getCoverAttribute($cover = '') {
        if (!$cover || !Storage::exists($this->getRealStorageCoverPath($cover))) {
            return (string) $cover;
        }

        return Storage::url($this->getRealStorageCoverPath($cover));
    }

    public function userStat() {
        return $this->hasOne(UserStat::class);
    }

    public function getCreatedAtAttribute($data = '') {
        return strtotime($data);
    }

    /**
     * @param  \Illuminate\Http\UploadedFile|array|null $file
     */
    public function saveCoverByFile($file) {
        $fileName = md5(uniqid(time(), true)) . '.' . $file->getClientOriginalExtension();
        $this->cover = $fileName;
        Storage::putFileAs(self::COVER_FOLDER, $file, $fileName);
    }

    private function getRealStorageCoverPath($cover = '') {
        $cover = $cover ?: $this->cover;
        return self::COVER_FOLDER . '/' . $cover;
    }

    /**
     * @return array
     */
    public function getShortInfo() {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "nickname" => $this->nickname,
        ];
    }

    /**
     * @return array
     */
    public function getBaseInfo() {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "cover" => $this->cover,
            "nickname" => $this->nickname,
        ];
    }

    /**
     * @return array
     */
    public function getFullInfo() {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "nickname" => $this->nickname,
            "cover" => $this->cover,
            "email" => $this->email,
            "favorite_team" => $this->favorite_team,
            "dt_birthday" => $this->dt_birthday,
            "push_notification" => $this->push_notification,
            "show_profile" => $this->show_profile,
            "ispremium_membership" => $this->ispremium_membership,
        ];
    }

    /**
     * @return array
     */
    public function getInfoWithCheck() {
        if (!$this->show_profile) {
            return $this->getBaseInfo();
        }

        return $this->getInfoWithoutCheck();
    }

    /**
     * @return array
     */
    public function getInfoWithoutCheck() {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "nickname" => $this->nickname,
            "cover" => $this->cover,
            "favorite_team" => $this->favorite_team,
            "dt_birthday" => $this->dt_birthday,
            "stats" => $this->userStat,
        ];
    }

    public function validatePassword($password) {
        return $this->password == $password;
    }

}
