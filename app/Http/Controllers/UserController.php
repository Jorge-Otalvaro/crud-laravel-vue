<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class UserController extends Controller
{   
    public function index(Request $request)
    {
        if (request()->wantsJson()) {

            $criterion      = $request->criterion;
            $search         = $request->search;

            if ($search == '') {
                $users = User::orderBy('id', 'DESC')->paginate(10);
            } else {
                $users = User::where($criterion, 'like', '%' . $search . '%')->orderBy('id', 'DESC')->paginate(10);
            }

            return [
                'pagination' => [
                    'total'        => $users->total(),
                    'current_page' => $users->currentPage(),
                    'per_page'     => $users->perPage(),
                    'last_page'    => $users->lastPage(),
                    'from'         => $users->firstItem(),
                    'to'           => $users->lastItem(),
                ],
                'users' => $users,
            ];
        }
        
        return view('home');            
    }

    public function store(Request $request)
    {
        if (request()->wantsJson()) {

            $existe = User::where('email', $request->email)->select('email')->get();

            if($existe->count() > 0){
                return ['info' =>'existe']; 
            }

            $user = User::create([
                'name'              => $request->name,
                'email'             => $request->email,
                'password'          => bcrypt($request->password),
            ]);

            return ['info' =>'Usuario registrado'];
        }
    }

    public function update(Request $request, $user)
    {
        if (request()->wantsJson()) {

            $existeEmail = User::where('email', $request->email)->where('id', '<>', $request->id)->select('email')->get();    

            if($existeEmail->count() > 0 ){
                return ['info' =>'existe']; 
            }

            $user           = User::findOrFail($user);
            $user->name     = $request->name;
            $user->email    = $request->email;            
            $user->save();

            return ['info' =>'Usuario actualizado'];
        }
    }

    public function destroy(Request $request, $user)
    {
        if (request()->wantsJson()) {

            $user = User::findOrFail($user);
            $user->delete();

            return ['info' =>'Usuario eliminado'];
        }
    }
}
