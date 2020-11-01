<?php

namespace App\Policies;

use App\User;
use App\Product;
use Illuminate\Auth\Access\HandlesAuthorization;
 
class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Can Access Product
     */
    public function accessCompanyPages(User $user, Product $product)
    {  
        return $user->company_id == $product->company_id;
    }

    public function accessFreeUsers(User $user)
    {    
      
        // role 6 : Free Users
        $allowedRoles = array(6);
        return in_array($user->role, $allowedRoles);
    }

    public function accessBasicUsers(User $user)
    {   
        // role 7 : Basic Users
        $allowedRoles = array(7);
        return in_array($user->role, $allowedRoles);
    }

    public function accessPremiumUsers(User $user)
    {   
        // role 8 : Premium Users
        $allowedRoles = array(8);
        return in_array($user->role, $allowedRoles);
    }

    public function accessTeamAdmin(User $user)
    {
        $allowedRoles = array(1,2, 3);
        return in_array($user->role, $allowedRoles);
    }

    public function accessTeamEditor(User $user)
    { 
        
        $allowedRoles = array(1,2, 3, 4);
        return in_array($user->role, $allowedRoles);
    }

    public function accessSuperAdmin(User $user)
    {
        return $user->role === 1;
    }
}
