<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\Team;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CreateTeamRequest;
use App\Http\Requests\UpdateTeamRequest;

class TeamController extends Controller
{
    public function fetch(Request $request){

        $id = $request->input('id');
        $name = $request->input('name');
        $limit = $request->input('limit', 10);

        $teamQuery  = Team::query();

        //Ambil satu data
        if($id){
            $team = $teamQuery->find($id);

            if($team){
                return ResponseFormatter::success($team, 'Team $id Found');
            }

            return ResponseFormatter::error('Team $id Not Found', 404);
        }

        //Ambil banyak data
        $teams = $teamQuery->where('company_id', $request->company_id);

        //Ambil banyak data berdasarkan filter nama team
        if($name){
            $teams->where('name', 'like', '%' . $name . '%');
        }

        return ResponseFormatter::success($teams->paginate($limit), 'Teams Found');
    }

    public function create(CreateTeamRequest $request){

        try {
            //upload icon
            if($request->hasFile($request->icon)){
                $path = $request->file('icon')->store('public/icons');
            }

            //create team
            $team = Team::create([
                'name' => $request->name,
                'icon' => $path,
                'company_id' => $request->company_id
            ]);

            if(!$team){
                throw new Exception('Team Not Created');
            }

            return ResponseFormatter::success($team, 'Team Created');

        } catch (Exception $error) {
            return ResponseFormatter::error($error->getMessage(), 500);
        }

    }

    public function update(UpdateTeamRequest $request, $id){

        try {
            $team = Team::find($id);

            //Check if Team Exist
            if(!$team){
                throw new Exception('Team Not Found');
            }

            //Upload icon
            if($team->hasFile('icon')){
                $path = $request->file('icon')->store('public/icons');
            }

            // Update Team
            $team->update([
                'name' => $request->name,
                'icon' => isset($path) ? $path : $team->icon,
                'company_id' => $request->company_id
            ]);

            return ResponseFormatter::success($team, 'Team Updated');
        } catch (Exception $error) {
            return ResponseFormatter::error($error->getMessage(), 500);
        }
    }

    public function destroy($id){


        try {
            //Get Team
            $team = Team::find($id);

            //Check if team exist
            if(!$team){
                throw new Exception('Team Not Found');
            }

            //delete team
            $team->delete();

            return ResponseFormatter::success($team, 'Team Deleted');


        } catch (Exception $error) {
            return ResponseFormatter::error($error->getMessage(), 500);
        }

    }

}
