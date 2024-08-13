<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function deactivate(Request $request, $id)
    {

        $user = User::findOrFail($id);

        $user->is_active =  $request->is_activate ? 0 : 1;
        $user->save();
    }

    public function delete($id)

    {
        $brand = User::findOrFail($id);
        $brand->delete();
    }
}
