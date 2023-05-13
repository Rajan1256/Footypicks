<?php

namespace App\Console\Commands;
use App\Models\League;
use App\Models\Schedule;
use App\Models\Team;
use App\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Console\Command;

class Recent_form_team extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'df:fm';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    //protected $client;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
       // $this->client = new Client();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $tm_data = Team::get();
        foreach ($tm_data as $row) {
        $query = Schedule::query()->with(['teamHome', 'teamAway']);

        $statusCollection = null;
//        if($request->has('status')) {
//            $statusCollection = $request->input('status');
//            if (!is_array($statusCollection)) {
//                $statusCollection = explode(',', $statusCollection);
//            }
//        }
//

            $id =$row->id;
        //$id=178;
            $query->orWhere(function ($query) use ($statusCollection,$id) {
                $query->where('team_away_id', $id);
                if (isset($statusCollection[0])) {
                    $query->whereIn('status', Schedule::prepareArrayStatus($statusCollection));
                }
            });

            $query->orWhere(function ($query) use ($statusCollection, $id) {
                $query->where('team_home_id', $id);
                if (isset($statusCollection[0])) {
                    $query->whereIn('status', Schedule::prepareArrayStatus($statusCollection));
                }
            });

            $collections = $query->orderBy('start_game_time')->get();
            $ary = $collections->map(function ($model) use ($id) {
                return $model->getInfoForTeam($id);
            });

            $var = '';

            for ($i = 0; $i < count($ary); $i++) {

                if ($ary[$i]['status'] == "FINISHED") {
                    $temp1 = '';


                    if ($ary[$i]['goals_away_team'] == $ary[$i]['goals_home_team']) {
                        $temp1 .= 'D';
                    } else if ($ary[$i]['is_home'] == true) {
                        if ($ary[$i]['goals_away_team'] < $ary[$i]['goals_home_team']) {
                            $temp1 .= 'W';

                        } else {
                            $temp1 .= 'L';

                        }
                    } else {
                        if ($ary[$i]['goals_away_team'] > $ary[$i]['goals_home_team']) {
                            $temp1 .= 'W';
                        } else {
                            $temp1 .= 'L';
                        }
                    }

                    //echo "data : " . $temp1;
                    $var .= $temp1;


                }
            }
        echo "data:".$var;
        $st = DB::table('teams')->where('id', $id)->update([
            'recentform' => strrev($var)
        ]);

    }
    }
}
