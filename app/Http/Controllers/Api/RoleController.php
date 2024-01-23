<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {

        $loggedInUser   = $request->user();
        $slug           = $loggedInUser->roles[0]->slug;

        // SUPER ADMIN: can view all user
        if($loggedInUser->roles[0]->slug == 'super_admin'){
            return Role::all();
        }

        // ADMIN: Only all user on same hospital
        if($loggedInUser->roles[0]->slug == 'admin'){
            return Role::where('slug', '!=', 'super_admin')->get();
        }

        //return $this->generate_lower_role($slug);
    }

    static public function generate_lower_role($slug){
        
        $role           = Role::where('slug', $slug)->first();
        $roles          = Role::where('hierarchic_level', '>=', $role->hierarchic_level);
        $lower_roles    = [];
        foreach ($roles as $key => $value) {
            $lower_roles[] = $value->slug;
        }
        // $lower_roles    = [$role];
        // while( count($role->childs) > 0){
            
        //     $childs = $role->childs;
        //     for ($i=0; $i < count($childs); $i++) { 
        //         $lower_roles[] = $childs[$i];
        //     }
        //     $role = $childs[0];
        // }
        return $lower_roles;
    }

    static public function generate_strict_lower_role($slug, $attribut){
        
        $role           = Role::where('slug', $slug)->first();
        $roles          = Role::where('hierarchic_level', '>', $role->hierarchic_level);
        $lower_roles    = [];
        foreach ($roles as $key => $value) {
            $lower_roles[] = $value->slug;
        }
        
        // while( count($role->childs) > 0){
            
        //     $childs = $role->childs;
        //     for ($i=0; $i < count($childs); $i++) { 
        //         $lower_roles[] = $childs[$i];
        //     }
        //     $role = $childs[0];
        // }

        // if($attribut == 'slug'){
        //     $array_slug = [];
        //     for ($i=0; $i < count($lower_roles); $i++) { 
        //         $array_slug[] = $lower_roles[$i]->slug;
        //     }
        //     return $array_slug;
        // }

        return $lower_roles;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $data = $request->validate([
            'name' => 'required',
            'slug' => 'required',
        ]);

        $existing = Role::where('slug', $data['slug'])->first();

        if (! $existing) {
            $role = Role::create([
                'name' => $data['name'],
                'slug' => $data['slug'],
            ]);

            return $role;
        }

        return response(['error' => 1, 'message' => 'role already exists'], 409);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Role  $role
     * @return \App\Models\Role $role
     */
    public function show(Role $role) {
        return $role;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response|Role
     */
    public function update(Request $request, Role $role = null) {
        if (! $role) {
            return response(['error' => 1, 'message' => 'role doesn\'t exist'], 404);
        }

        $role->name = $request->name ?? $role->name;

        if ($request->slug) {
            if ($role->slug != 'admin' && $role->slug != 'super-admin') {
                //don't allow changing the admin slug, because it will make the routes inaccessbile due to faile ability check
                $role->slug = $request->slug;
            }
        }

        $role->update();

        return $role;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role) {
        if ($role->slug != 'admin' && $role->slug != 'super-admin') {
            //don't allow changing the admin slug, because it will make the routes inaccessbile due to faile ability check
            $role->delete();

            return response(['error' => 0, 'message' => 'role has been deleted']);
        }

        return response(['error' => 1, 'message' => 'you cannot delete this role'], 422);
    }
}
