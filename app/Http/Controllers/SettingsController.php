<?php

namespace App\Http\Controllers;

use App\User;
use App\Company;
 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
 
class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Client Organization
     */
    public function fetchOrg()
    {
        $company = Company::where('id', Auth::user()->company_id)->firstOrFail();
        return response()->json($company, 200);
    }

    public function fetchAccount()
    {
        $account = User::where('id', Auth::user()->id)->firstOrFail();
        return response()->json($account, 200);
    }

    public function updateAccount_password(Request $request)
    {   
        
        $request['password'] = Hash::make($request->password);
         
        $account = User::where('id', Auth::user()->id)->first(); 
        $account->update($this->validatePassword());
        return response()->json([
            'message' => 'Account has been updated.',
        ], 200);
    }

    public function updateAccount(Request $request)
    {
        $account = User::where('id', Auth::user()->id)->first(); 
        $account->update($this->validateAccountequest());
        return response()->json([
            'message' => 'Account has been updated.',
        ], 200);
    }

    public function getOrgUsers($id)    
    {   
        if(Auth::user()->id > 1){
            $users = User::with('permissions')->where('company_id', $id)->where('role', "<>", 1)->orderBy('role', 'asc')->orderBy('created_at', 'desc')->paginate(10);
            //$users = User::where('company_id', $id)->where('role', "<>", 1)->orderBy('role', 'asc')->orderBy('created_at', 'desc')->paginate(10);
        }else{
            $users = User::with('permissions')->where('company_id', $id)->orderBy('role', 'asc')->orderBy('role', 'asc')->orderBy('created_at', 'desc')->paginate(10);
        }
        return response()->json($users, 200);
    }

    public function updateOrg(Request $request)
    {

        $user = $request->user();
        if(!$user->can('all-permission') && !$user->can('u-only')){
            return response()->json([
                'message' => 'You dont have permission / cannot remove remaining admin',
            ], 422);
        }

        $company = Company::where('id', Auth::user()->company_id)->firstOrFail();
        $company->update($this->validateOrgRequest());
        return response()->json([
            'message' => 'Organization has been updated.',
        ], 200);
    }

    /**
     * Client Teams
     */
    public function deleteOrgUser($id)
    {
        $user = User::where('id', $id)->firstOrFail(); 
         
        if(!$user->can('all-permission') && !$user->can('d-only')  && $this->hasOneAdmin() == false){
                return response()->json([
                    'message' => 'You dont have permission / cannot remove remaining admin',
                ], 422);
        }
         

        $user->delete();
        return response()->json([
            'message' => 'User has been deleted',
        ], 200);
    }
    public function updateOrgUser(Request $request, $id)
    {
        $user = User::where('id', $id)->firstOrFail();
        if(!$user->can('all-permission') && !$user->can('u-only') ){
                return response()->json([
                    'message' => 'You dont have permission',
                ], 422);
          
        }
        $user->update($this->validateOrgTeamRequest());
        $user->roles()->sync($request->role); // adding role_id 3 on table users_roles    
        $user->permissions()->sync($request->permissions); // adding permision id 2 - 5 on table users_permissions
        return response()->json([
            'message' => 'User has been updated',
        ], 200);
    }

    public function searchData($search) { 
        $company_id = Auth::user()->company_id;
        $user = User::where('company_id', $company_id)->where('name', 'like', '%'.$search.'%')->orWhere('phone', 'like', '%'.$search.'%')->orderBy('name', 'asc')->paginate(10);
        
        return response()->json($user, 200);
    }

    public function saveOrgUser(Request $request)
    {
        $this->validateNewTeamRequest(); 
        $user = $request->user();
        if(!$user->can('all-permission') && !$user->can('c-only')){
            return response()->json([
                'message' => 'You dont have permission',
            ], 422);
      
        }
       
        // store request
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'phone' => $request->phone,
            'status' => 1,
            'password' => Hash::make($request->password),
            'company_id' => Auth::user()->company_id
        ]); 
        $user->roles()->sync($request->role); // adding role_id 3 on table users_roles    
        $user->permissions()->sync($request->permissions); // adding permision id 2 - 5 on table users_permissions
       
        // response
        return response()->json([  
            'message' => 'User has been created',
        ], 200);
    }

    public function validateNewTeamRequest()
    {
        return request()->validate([
            'name' => ['required', 'min:3', 'max:50', 'string'],
            'email' => ['sometimes', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'min:8'],
            'phone' => [''],
            'role' => ['integer'],
        ]);
    }

    public function validateOrgTeamRequest()
    {
        return request()->validate([
            'name' => ['required', 'min:1', 'max:50', 'string'],
            'email' => ['sometimes', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => [''],
            'role' => ['integer'],
        ]);
    } 
    
    public function validatePassword()
    {
        return request()->validate([ 
            'password' => ['required', 'min:8']             
        ]);

    }

    public function validateOrgRequest()
    {
        return request()->validate([
            'logo' => [''],
            'title' => ['required', 'min:1', 'max:50', 'string'],
            'description' => [''],
        ]);

    }

    public function validateAccountequest(){
        return request()->validate([
           
            'name' => ['required', 'min:5', 'max:80', 'string'],
            'phone' => [''],
        ]);
    }

    public function hasOneAdmin()
    {
        $teamAdmins = User::where([
            'company_id' => Auth::user()->company_id,
            'role' => 3,
        ])->get();

        if($teamAdmins->count() > 1 ){
            return true;
        }
        return false;
    }
}
