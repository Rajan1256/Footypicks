<?php

namespace App\Models;

use Illuminate\Support\Facades\Storage;

class League extends Base
{
    const COVER_FOLDER = 'leagues';

    const STATUS_FINISH = 5;

    protected $fillable = array(
        'caption',
        'name',
        'cover',
        'teams_count',
        'games_count',
        'league_parse_id',
        'parse_id_v2',
        'last_updated',
        'current_matchday',
	'match_stage',
        'matchdays_count',
        'status',
    );

    protected $hidden = [
        'league_parse_id',
        // 'parse_id_v2',
        'last_updated',
        'status',
        'created_at',
        'updated_at',
    ];

    /**
     * @param  \Illuminate\Http\UploadedFile|array|null $file
     */
    public function saveCoverByFile($file)
    {
        $fileName = md5(uniqid(time(), true)) . '.' . $file->getClientOriginalExtension();
        $this->cover = $fileName;
        Storage::putFileAs(self::COVER_FOLDER, $file, $fileName);
    }

    public function getCoverAttribute($cover = '')
    {
        if (!$cover || !Storage::exists($this->getRealStorageCoverPath($cover))) {
            return (string)$cover;
        }

        return Storage::url($this->getRealStorageCoverPath($cover));
    }

    private function getRealStorageCoverPath($cover = '')
    {
        $cover = $cover ?: $this->cover;
        return self::COVER_FOLDER . '/' . $cover;
    }

    public function teams()
    {
        return $this->hasMany(Team::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
}
