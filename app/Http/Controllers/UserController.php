<?php

namespace App\Http\Controllers;

use App\Models\Bitacora;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->orderBy('name')->get();
        $roles = Role::orderBy('name')->get();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name_create' => 'required|max:255',
            'email_create' => 'required|email|unique:users,email',
            'password_create' => 'required|min:8',
            'role_create' => 'required|exists:roles,name',
        ]);

        $user = User::create([
            'name' => $request->name_create,
            'email' => $request->email_create,
            'password' => Hash::make($request->password_create),
            'email_verified_at' => now('America/La_Paz'),
        ]);

        $user->assignRole($request->role_create);

        Bitacora::create([
            'user_id' => auth()->user()->id,
            'accion' => 'Se creó un usuario: ' . $user->name,
            'hora' => now('America/La_Paz'),
        ]);

        return redirect()->route('admin.users.index')
            ->with('mensaje', 'Usuario creado correctamente.')
            ->with('icono', 'success');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|min:8',
            'role' => 'required|exists:roles,name',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();
        $user->syncRoles([$request->role]);

        Bitacora::create([
            'user_id' => auth()->user()->id,
            'accion' => 'Se editó un usuario: ' . $user->name,
            'hora' => now('America/La_Paz'),
        ]);

        return redirect()->route('admin.users.index')
            ->with('mensaje', 'Usuario actualizado correctamente.')
            ->with('icono', 'success');
    }

    public function destroy($id)
    {
        if (auth()->id() == $id) {
            return redirect()->route('admin.users.index')
                ->with('mensaje', 'No puede eliminar su propia cuenta.')
                ->with('icono', 'error');
        }

        $user = User::findOrFail($id);
        $name = $user->name;
        $user->delete();

        Bitacora::create([
            'user_id' => auth()->user()->id,
            'accion' => 'Se eliminó un usuario: ' . $name,
            'hora' => now('America/La_Paz'),
        ]);

        return redirect()->route('admin.users.index')
            ->with('mensaje', 'Usuario eliminado correctamente.')
            ->with('icono', 'success');
    }
}
