<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\Responsibility;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateResponsibilityRequest;
use App\Http\Requests\UpdateResponsibilityRequest;

class ResponsibilityController extends Controller
{
    public function fetch(Request $request){

        $id = $request->input('id');
        $name = $request->input('name');
        $limit = $request->input('limit', 10);

        $responsibilityQuery  = Responsibility::query();

        //Ambil satu data
        if($id){
            $responsibility = $responsibilityQuery->find($id);

            if($responsibility){
                return ResponseFormatter::success($responsibility, 'Responsibility $id Found');
            }

            return ResponseFormatter::error('Responsibility $id Not Found', 404);
        }

        //Ambil banyak data
        $responsibilities = $responsibilityQuery->where('role_id', $request->role_id);

        //Ambil banyak data berdasarkan filter nama responsibility
        if($name){
            $responsibilities->where('name', 'like', '%' . $name . '%');
        }

        return ResponseFormatter::success($responsibilities->paginate($limit), 'Responsibilities Found');
    }

    public function create(CreateResponsibilityRequest $request){

        try {
            //create responsibility
            $responsibility = Responsibility::create([
                'name' => $request->name,
                'role_id' => $request->role_id
            ]);

            if(!$responsibility){
                throw new Exception('Responsibility Not Created');
            }

            return ResponseFormatter::success($responsibility, 'Responsibility Created');

        } catch (Exception $error) {
            return ResponseFormatter::error($error->getMessage(), 500);
        }

    }

    public function destroy($id){


        try {
            //Get Responsibility
            $responsibility = Responsibility::find($id);

            //Check if responsibility exist
            if(!$responsibility){
                throw new Exception('Responsibility Not Found');
            }

            //delete responsibility
            $responsibility->delete();

            return ResponseFormatter::success($responsibility, 'Responsibility Deleted');


        } catch (Exception $error) {
            return ResponseFormatter::error($error->getMessage(), 500);
        }

    }
}
