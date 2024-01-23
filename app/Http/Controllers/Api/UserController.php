<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\Exceptions\MissingAbilityException;
use App\Http\Controllers\Api\RoleController;
use UserAccess;
use LogActivity;
use Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $loggedInUser = $request->user();
        $loggedInUser['service']['hopital'] = $loggedInUser->service->hopital;  

        // SUPER ADMIN: can view all user
        if($loggedInUser->roles[0]->slug == 'super_admin'){
            return User::with('roles', 'service','service.hopital')->get();
        }

        // ADMIN: Only all user on same hospital
        if($loggedInUser->roles[0]->slug == 'admin'){

            $hopital_id = $loggedInUser->service->hopital->id;

            $users = User::whereIn('service_id', 
                                    function($query) use ($hopital_id){ 
                                        $query->select('id')->from('services')->where('hopital_id', $hopital_id); 
                                    })
                        ->with('roles', 'service','service.hopital')
                        ->get();

            //Filter only user with lower Role
            $role               = Role::where('slug', 'admin')->first();
            $roles              = Role::where('hierarchic_level', '>=', $role->hierarchic_level)->get();
            $admin_lower_roles  = [];
            foreach ($roles as $key => $value) {
                $admin_lower_roles[] = $value->slug;
            }

            $filtered_user = [];
            for ($i=0; $i < count($users); $i++) { 
                if(in_array($users[$i]->roles[0]->slug, $admin_lower_roles)){
                    $filtered_user[] = $users[$i];
                } 
            }
            return $filtered_user;
        }


        //CHEF DE SERVICE: Only user on same service
        // if($loggedInUser->roles[0]->slug == 'chef_service'){
            
        //     $service_id = $loggedInUser->service->id;
            
        //     $users = User::where('service_id', $service_id)
        //                     ->with('roles', 'service','service.hopital')
        //                     ->get();

        //     //Filter only user with lower Role
        //     $role               = Role::where('slug', 'chef_service')->first();
        //     $roles              = Role::where('hierarchic_level', '>=', $role->hierarchic_level)->get();
        //     $chef_service_lower_roles  = [];
        //     foreach ($roles as $key => $value) {
        //         $chef_service_lower_roles[] = $value->slug;
        //     }
            
        //     $filtered_user = [];
        //     for ($i=0; $i < count($users); $i++) { 
        //         if(in_array($users[$i]->roles[0]->slug, $chef_service_lower_roles)){
        //             $filtered_user[] = $users[$i];
        //         } 
        //     }
        //     return $filtered_user;
        // }

        
        //MEDECIN: Only user on same service
        // if($loggedInUser->roles[0]->slug == 'medecin'){
            
        //     $service_id = $loggedInUser->service->id;
            
        //     $users = User::where('service_id', $service_id)
        //                     ->with('roles', 'service','service.hopital')
        //                     ->get();
            
        //     //Filter only user with lower Role
        //     $role               = Role::where('slug', 'medecin')->first();
        //     $roles              = Role::where('hierarchic_level', '>=', $role->hierarchic_level)->get();
        //     $medecin_lower_roles  = [];
        //     foreach ($roles as $key => $value) {
        //         $medecin_lower_roles[] = $value->slug;
        //     }
            
        //     $filtered_user = [];
        //     for ($i=0; $i < count($users); $i++) { 
        //         if(in_array($users[$i]->roles[0]->slug, $medecin_lower_roles)){
        //             $filtered_user[] = $users[$i];
        //         } 
        //     }
        //     return $filtered_user;
        // }

        //INTERN: Only user on same service
        // if($loggedInUser->roles[0]->slug == 'intern'){
            
        //     $service_id = $loggedInUser->service->id;
            
        //     $users = User::where('service_id', $service_id)
        //                     ->with('roles', 'service','service.hopital')
        //                     ->get();
            
        //     //Filter only user with lower Role
        //     $admin_lower_roles = RoleController::generate_strict_lower_role('intern', 'slug');
            
        //     $filtered_user = [$loggedInUser];
        //     for ($i=0; $i < count($users); $i++) { 
        //         if(in_array($users[$i]->roles[0]->slug, $admin_lower_roles)){
        //             $filtered_user[] = $users[$i];
        //         } 
        //     }
        //     return $filtered_user;
        // }
    }

    public function hierarchie(){
        $parent_node    = User::with('roles', 'childs', 'service')->where('parent_id', 0)->get();
        //return $parent_node;
        $hierarchie     = $this->generate_tree($parent_node);
        return $hierarchie;
    }

    public function generate_tree($parent_node){
        $processed_data = [];
        foreach ($parent_node as $key => $node) {
            $current = [];
            $current['name']        = $node->name;
            $current['role']        = $node->roles[0]->name;
            $current['service']     = $node->roles[0]->name == "Chef de service" ? $node->service->name : '';
            $current['children']    = $this->generate_tree($node->childs);
            $processed_data[]       = $current;
        }
        return $processed_data;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

        $loggedInUser = Auth::user();
        if($loggedInUser->roles[0]->slug != 'super_admin' && $loggedInUser->roles[0]->slug != 'admin'){
            return response(['error' => 1, 'message' => 'Not authorized to create user'], 404);
        }

        $creds = $request->validate([
            'service_id'=> 'integer',
            'parent_id' => 'integer',
            'name'      => 'nullable|string',
            'role'      => 'required',
            'email'     => 'required|email',
            'password'  => 'required'
        ]);

        $user = User::where('email', $creds['email'])->first();
        if ($user) {
            return response(['error' => 1, 'message' => 'user already exists'], 409);
        }

        //$parent_id = 
        //$user['parent_id'] = intval($request['parent_id']);
        
        //return $request->all(); 

        if($request->hasFile('avatar')){

            $path = $request->file('avatar')->store('public/avatars');
            $storage_path = str_replace('public', 'storage', $path);
            $user = User::create([
                'service_id'=> $creds['service_id'],
                'parent_id' => empty($request['parent_id']) ? 0 : $creds['parent_id'],
                'name'      => $creds['name'],
                'avatar'    => $storage_path,
                'email'     => $creds['email'],
                'password'  => Hash::make($creds['password']),
            ]);
        }else{
            $user = User::create([
                'service_id'=> $creds['service_id'],
                'parent_id' => empty($request['parent_id']) ? 0 : $creds['parent_id'],
                'name'      => $creds['name'],
                'email'     => $creds['email'],
                'password'  => Hash::make($creds['password'])
            ]);
        }

        $user->roles()->attach(Role::where('slug', $creds['role'])->first());


        $loggedInUser = $request->user();
        LogActivity::addToLog($loggedInUser->name . ' create new User '.' with name: '. $user->name);
        return $user;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \App\Models\User  $user
     */
    public function show($id) {

        //Check for permission access
        $permission     = UserAccess::viewPermission($id);
        if($permission != 'write'){
            return response(['error' => 1, 'message' => 'Not authorized to view user'], 404);
        }

        $user = User::with('roles')->where('id', $id)->first();  

        $loggedInUser = Auth::user();
        LogActivity::addToLog($loggedInUser->name . ' viewed User '. $user->name. '\'s information');
        return $user;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return User
     *
     * @throws MissingAbilityException
     */
    public function update(Request $request, User $user) {

        //Check for permission access
        $permission     = UserAccess::viewPermission($user->id);
        if($permission != 'write'){
            return response(['error' => 1, 'message' => 'Not authorized to update this user'], 404);
        }

        $user->service_id   = $request->service_id          ?? $user->service_id;
        $user->parent_id    = $request->parent_id           ?? $user->parent_id;
        $user->name         = $request->name                ?? $user->name;
        $user->email        = $request->email               ?? $user->email;
        $user->password     = isset($request->password) && $request->password != 'null' ? Hash::make($request->password) : $user->password;

        if($request->hasFile('avatar')){
            $path           = $request->file('avatar')->store('public/avatars');
            $storage_path   = str_replace('public', 'storage', $path);
            $user->avatar   = $storage_path ?? $user->avatar;
        }
        
        if($request->role){
            $user->roles()->sync([Role::where('slug',$request->role)->first()->id]);
        }    

        $user->update();

        $loggedInUser   = $request->user();
        LogActivity::addToLog($loggedInUser->name . ' update the User data '.' with name: '. $user->name);
        
        //check if the logged in user is updating it's own record
        //$loggedInUser = $request->user();
        //if ($loggedInUser->id == $user->id) {
        //    $user->update();
        //} elseif ($loggedInUser->tokenCan('super_admin') ) {
        //    $user->update();
        //} else {
        //    throw new MissingAbilityException('Not Authorized');
        //}

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user) {

        //Check for permission access
        $permission     = UserAccess::viewPermission($user->id);
        if($permission != 'write'){
            return response(['error' => 1, 'message' => 'Not authorized to update this user'], 404);
        }

        $adminRole = Role::where('slug', 'admin')->first();
        $userRoles = $user->roles;

        if ($userRoles->contains($adminRole)) {
            //the current user is admin, then if there is only one admin - don't delete
            $numberOfAdmins = Role::where('slug', 'admin')->first()->users()->count();
            if (1 == $numberOfAdmins) {
                return response(['error' => 1, 'message' => 'Create another admin before deleting this only admin user'], 409);
            }
        }

        $user->delete();

        $loggedInUser   = Auth::user();
        LogActivity::addToLog($loggedInUser->name . ' deleted the User data '.' with name: '. $user->name);
        return response(['error' => 0, 'message' => 'user deleted']);
    }

}
