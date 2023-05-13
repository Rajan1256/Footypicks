<?php

namespace App\Models;
use App\PostComment;
use App\PostLike;
use Illuminate\Support\Facades\Auth;
use Storage;
use Illuminate\Database\Eloquent\Model;
//use Zend\Diactoros\Request;
use Illuminate\Http\Request;
class Feeds extends Model
{
    const FEEDS_FOLDER = 'feeds';

    protected $fillable = array(
        'feed_file',
        'feed_content',
        'feed_height',
        'feed_width',
        'created_at',
        'updated_at',
    );

     /**
     * @param  \Illuminate\Http\UploadedFile|array|null $file
     */
    public function saveFeedByFile($file)
    {
        $fileName = md5(uniqid(time(), true)) . '.' . $file->getClientOriginalExtension();
        $this->feed_file = $fileName;
        $this->feed_thumb = "";

        Storage::putFileAs(self::FEEDS_FOLDER, $file, $fileName);
        if(in_array($file->getClientOriginalExtension(), ["mov", "mp4"])) {
            $this->saveFileThumb($fileName);
        }
    }

    // public function saveFileThumb($vfileName)
    // {
    //     $fileName = md5(uniqid(time(), true)) . '.jpg';
    //     // $this->feed_thumb = $fileName;
    //     $videoUrl = public_path("storage/".self::FEEDS_FOLDER ."/". $vfileName);
    //     $storageUrl = public_path("storage/".self::FEEDS_FOLDER ."/thumb/".$fileName);
    //     $second = 2;
    //     // VideoThumbnail::createThumbnail(public_path('files/$fileName), public_path('files/thumbs/'), 'movie.jpg', 2, 1920, 1080);
    //     \VideoThumbnail::createThumbnail($videoUrl, $storageUrl, $fileName, $second, 640, 480);
    // }

    
    public function saveFileThumb($vfileName)
    {
        $fileName = md5(uniqid(time(), true)) . '.jpg';
        $this->feed_thumb = $fileName;
        $videoUrl = public_path("storage/".self::FEEDS_FOLDER ."/". $vfileName);

        $storageUrl = public_path("storage/".self::FEEDS_FOLDER ."/thumb/".$fileName);

        $second = 1;
        
        $thumbSize   = '640x480';
        
        // Video file name without extension(.mp4 etc)
        $videoname  = 'sample_video';
        
        // FFmpeg Command to generate video thumbnail
        
        // $cmd = "ffmpeg -i  $videoUrl -deinterlace -an -ss $second -t 00:00:01  -s $thumbSize -r 1 -y -vcodec mjpeg -f mjpeg $storageUrl 2>&1";
        $cmd = "ffmpeg -i  $videoUrl -deinterlace -an -ss $second -t 00:00:01 -r 1 -y -vcodec mjpeg -f mjpeg $storageUrl 2>&1";
        // dd($cmd );
        exec($cmd, $output, $retval);
 
        if ($retval)
        {
            // echo 'error in generating video thumbnail');
        }
        else
        {
            // dd('Thumbnail generated successfully');
            // echo $thumb_path = $thumbnail_path . $videoname . '.jpg';
        }
     }
    
 public function like()
    {
        return $this->hasMany(PostLike::class,  'post_id','id')->where('flag_like','true');
    }

 public function like_current()
    {
       return $this->hasone(PostLike::class,  'post_id','id')->where('user_id',request()->user()->id);
    }
    public function post()
    {
        return $this->hasMany(PostComment::class,   'post_id','id');
    }   
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFeedFileAttribute($feed_file = '')
    {
        if (!$feed_file) {
            return NULL;
        } else if( !Storage::exists($this->getRealFeedFileCoverPath($feed_file))) {
            return (string) $feed_file;
        }

        return Storage::url($this->getRealFeedFileCoverPath($feed_file));
    }

    public function getFeedThumbAttribute($feed_thumb = '')
    {
         if (!$feed_thumb) {
            return NULL;
        }
        return Storage::url($this->getRealFeedFilethumb($feed_thumb));
    }

    private function getRealFeedFilethumb($feed_file = '')
    {
        $feed_file = $feed_file ?: $this->feed_file;
        return self::FEEDS_FOLDER . '/thumb/' . $feed_file;
    }

    // public function saveCoverByFile($file)
    // {
    //     $fileName = md5(uniqid(time(), true)) . '.' . $file->getClientOriginalExtension();
    //     $this->cover = $fileName;
    //     Storage::putFileAs(self::COVER_FOLDER, $file, $fileName);
    // }

    private function getRealFeedFileCoverPath($feed_file = '')
    {
        $feed_file = $feed_file ?: $this->feed_file;
        return self::FEEDS_FOLDER . '/' . $feed_file;
    }

}
