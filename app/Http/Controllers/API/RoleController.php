<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateRoleRequest;
use App\Http\Requests\UpdateRoleRequest;

class RoleController extends Controller
{
    public function fetch(Request $request){

        $id = $request->input('id');
        $name = $request->input('name');
        $limit = $request->input('limit', 10);
        $with_responsibilities = $request->input('with_responsibilities', false);

        $roleQuery  = Role::withCount('responsibilities');

        //Ambil satu data
        if($id){
            $role = $roleQuery->with('responsibilities')->find($id);

            if($role){
                return ResponseFormatter::success($role, 'Role $id Found');
            }

            return ResponseFormatter::error('Role $id Not Found', 404);
        }

        //Ambil banyak data
        $roles = $roleQuery->where('company_id', $request->company_id);

        //Ambil banyak data berdasarkan filter nama role
        if($name){
            $roles->where('name', 'like', '%' . $name . '%');
        }

        if($with_responsibilities){
            $roles->with('responsibilities');
        }

        return ResponseFormatter::success($roles->paginate($limit), 'Roles Found');
    }

    public function create(CreateRoleRequest $request){

        try {
            //create role
            $role = Role::create([
                'name' => $request->name,
                'company_id' => $request->company_id
            ]);

            if(!$role){
                throw new Exception('Role Not Created');
            }

            return ResponseFormatter::success($role, 'Role Created');

        } catch (Exception $error) {
            return ResponseFormatter::error($error->getMessage(), 500);
        }

    }

    public function update(UpdateRoleRequest $request, $id){

        try {
            $role = Role::find($id);

            //Check if Role Exist
            if(!$role){
                throw new Exception('Role Not Found');
            }

            // Update Role
            $role->update([
                'name' => $request->name,
                'company_id' => $request->company_id
            ]);

            return ResponseFormatter::success($role, 'Role Updated');
        } catch (Exception $error) {
            return ResponseFormatter::error($error->getMessage(), 500);
        }
    }

    public function destroy($id){


        try {
            //Get Role
            $role = Role::find($id);

            //Check if role exist
            if(!$role){
                throw new Exception('Role Not Found');
            }

            //delete role
            $role->delete();

            return ResponseFormatter::success($role, 'Role Deleted');


        } catch (Exception $error) {
            return ResponseFormatter::error($error->getMessage(), 500);
        }

    }
}
