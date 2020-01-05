<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Cycles
{
    public function __construct()
    {

    }

    public function getChannelsIDs()
    {
        $tmp = DB::table('channels')->select('channel_id')
            ->where('active', true)->get();

        $channelsIDs = $tmp->all();

        return $channelsIDs;
    }

    public function getVideosByChannelId( $channelID )
    {
        $videos = DB::table('videos')->where('channel_id', $channelID)->get();

        return $videos;
    }

    /**
     * all channels all videos last cycle
     * @return array of videos
     */
    public function getLastCycle()
    {
        $videos = DB::select(
            'SELECT channel_id, video_id, tags, cycle_no, view_count, rating
            FROM videos WHERE (video_id,cycle_no) IN
            ( SELECT video_id, MAX(cycle_no)
              FROM videos
              GROUP BY video_id
            )');

        return $videos;
    }

    /**
     * Prepare new cycle data and inset in to new DB record
     * @return VOID
     */
    public function countNewCycle()
    {
        $data = $this->getLastCycle();

        $channelRating = $this->getChannelRating();

        $now = Carbon::now();

        foreach ($data as $video) {

            $vid = get_object_vars($video);

            $new = \Youtube::getVideoInfo($vid['video_id'], ['statistics']); //'id', 'snippet',
            dd($new);
            DB::table('videos')->insert([
                'channel_id' => $vid['channel_id'],
                'video_id'   => $vid['video_id'],
                'tags'       => $vid['tags'],
                'cycle_no'   => $vid['cycle_no'] + 1,
                'view_count' => $new->statistics->viewCount - $vid['view_count'],
                'rating'     => $new->statistics->viewCount / $channelRating,
                'created_at' => $now,
                'updated_at' => $now
            ]);
        }
    }

    /**
     * import channel data from Youtube by channel ID from .env file
     * @return VOID record channel data to DB
     */
    public function importChannel()
    {
        $channelID = env('YOUTUBE_CHANNEL_ID');

        $channelData = Youtube::getChannelById($channelID,false,['snippet']); //, 'statistics'

        $now = Carbon::now();

        DB::table('channels')->insert([
            'channel_id'   => $channelID,
            'channel_name' => $channelData->snippet->title,
            'created_at' => $now,
            'updated_at' => $now
        ]);

        $videos = Youtube::listChannelVideos($channelID, 50);
        // for channels with videos over 50,
        // multi request with page tokens should be implemented here

        foreach ($videos as $k => $data) {

            $video = Youtube::getVideoInfo($data->id->videoId,['statistics']); //'id', 'snippet', 
            $tags = implode(',', $video->snippet->tags);

            DB::table('videos')->insert([
                'channel_id' => $channelID,
                'video_id'   => $data->id->videoId,
                'tags'       => $tags,
                'view_count' => $video->statistics->viewCount,
                'rating'     => 0,
                'created_at' => $now,
                'updated_at' => $now
            ]);
        }
    }

    /**
     * Calculate all channels in DB first hour views median and
     * @return VOID record it to DB
     */
    public function countChannelRating()
    {
        $channelIDs = $this->getChannelsIDs();
        // each channel id
        foreach ($channelIDs as $ch) {
            // all videos all views of same channel of first cycle
            $videos_views = DB::table('videos')
                ->select('view_count')
                ->where([
                    ['channel_id', $ch->channel_id],
                    ['cycle_no', '=', 1],
                ])->get();
            // extract array of views to array
            foreach ($videos_views->all() as $v) $views[] = $v->view_count;
            // dd(array_values($views));
            $median = $this->median($views);

            DB::table('channels')->where('channel_id', $ch->channel_id)
                ->update(['rating' => $median]);
        }
    }

    public function countVideoRating()
    {
        $videos = DB::select(
            'SELECT channel_id, video_id, cycle_no, view_count
            FROM videos WHERE (video_id,cycle_no) IN
            ( SELECT video_id, MIN(cycle_no)
              FROM videos
              GROUP BY video_id
            )');

        // here should be a loop through all channelsIDs...
        $channelRating = $this->getChannelRating();

        //... and inside of channelsIDs loop through channels videos.
        foreach ($videos as $video) {

            $values = get_object_vars($video);

            $rating = $values['view_count'] / $channelRating;

            DB::table('videos')->where([
                ['video_id', $values['video_id']],
                ['cycle_no', 1],
            ])->update(['rating' => $rating]);
        }
    }

    public function getChannelRating( $id = null )
    {
        if (!isset($id)) $id = env('YOUTUBE_CHANNEL_ID');

        $tmp = DB::table('channels')
                 ->select('rating')
                 ->where('channel_id', $id)->get();

        $ratingTmp = $tmp->all();

        return $ratingTmp[0]->rating;
    }

    /**
     * simple math function for median calculation
     * @param array of numbers
     * @return number the median
     */
    public function median( $numbers = [] )
    {
    	if (!is_array($numbers)) $numbers = func_get_args();

    	rsort($numbers);

    	$mid = (count($numbers) / 2);

        $median = ($mid % 2 != 0)
            ? $numbers{$mid-1}
            : (($numbers{$mid-1}) + $numbers{$mid}) / 2;

    	return $median;
    }
}
