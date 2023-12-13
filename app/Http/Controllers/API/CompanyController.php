<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CreateCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;

class CompanyController extends Controller
{
    // fungsi all sama dengan index
    public function fetch(Request $request){

        $id = $request->input('id');
        $name = $request->input('name');
        $limit = $request->input('limit', 10);

        $companyQuery  = Company::with(['users'])->whereHas('users', function($query){
            $query->where('user_id', Auth::id());
        });

        //Ambil satu data
        if($id){
            $company = $companyQuery->find($id);

            if($company){
                return ResponseFormatter::success($company, 'Data Company $id Ditemukan');
            }

            return ResponseFormatter::error('Data Company $id Tidak Ditemukan', 404);
        }

        //Ambil banyak data
        $companies = $companyQuery;

        //Ambil banyak data berdasarkan filter nama company
        if($name){
            $companies->where('name', 'like', '%' . $name . '%');
        }

        return ResponseFormatter::success($companies->paginate($limit), 'Data Comapnies Ditemukan');
    }

    public function create(CreateCompanyRequest $request){

        try {
            //upload logo
            if($request->hasFile($request->logo)){
                $path = $request->file('logo')->store('public/logos');
            }

            //create company
            $company = Company::create([
                'name' => $request->name,
                'logo' => $path
            ]);

            if(!$company){
                throw new Exception('Company Not Created');
            }

            //Attach Company to User
            $user = User::find(Auth::id());
            $user->companies()->attach($company->id);

            //Load Users at company
            $company->load('users');

            return ResponseFormatter::success($company, 'Company Created');
        } catch (Exception $error) {
            return ResponseFormatter::error($error->getMessage(), 500);
        }

    }

    public function update(UpdateCompanyRequest $request, $id){

        try {
            $company = Company::find($id);

            //Check if Company Exist
            if(!$company){
                throw new Exception('Company Not Found');
            }

            //Upload logo
            if($company->hasFile('logo')){
                $path = $request->file('logo')->store('public/logos');
            }

            // Update Company
            $company->update([
                'name' => $request->name,
                'logo' => isset($path) ? $path : $company->logo,
            ]);

            return ResponseFormatter::success($company, 'Company Updated');
        } catch (Exception $error) {
            return ResponseFormatter::error($error->getMessage(), 500);
        }
    }

}
