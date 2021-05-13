<?php

namespace App\Http\Middleware;

use App\Models\Role;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsAdminUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $id=Auth::user()->getAuthIdentifier();
        $user=User::where('id',$id)->first();
        $role=$user->role;
        if ($role->role == Role::ADMIN) {
            return $next($request);
        }
    }
}
