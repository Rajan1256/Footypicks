<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Article extends Model
{
    const COVER_FOLDER = 'post_covers';

    protected $fillable = array(
        'title',
        'info',
        'cover',
        'date_from',
        'date_to',
        'time_from',
        'time_to',
        'category_id',
        'is_private',
        'number_extra_tickets',
        'address',
        'lat',
        'lng',
        'status',
    );

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * @return User
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getUser()
    {
        return (isset($this->user)) ? $this->user : App::make('\App\Modules\VergoBase\Database\Models\User')->setDefault();
    }

    /**
     * @param  \Illuminate\Http\UploadedFile|array|null $file
     */
    public function saveCoverByFile($file)
    {
        $fileName = md5(uniqid(time(), true)) . '.' . $file->getClientOriginalExtension();
        $this->cover = $fileName;
        Storage::putFileAs(self::COVER_FOLDER, $file, $fileName);
    }
}
