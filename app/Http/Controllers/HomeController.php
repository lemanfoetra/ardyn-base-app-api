<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        try {
            // MENU ARE OPEN
            $menu = DB::table('api_modules')
                ->select(['menus.id', 'menus.menu', 'menus.link'])
                ->where('url', 'like', request()->segment(1) . '/' . request()->segment(2) . '%')
                ->join('menus', 'menus.id', '=', 'api_modules.id_menus')
                ->first();

            // LIST PERMISSION ON THIS MENU
            $permissions = [];
            $results = DB::table('role_menu_accesses')
                ->select(['access_code'])
                ->where('id_menus', $menu->id)
                ->where('id_roles', auth()->user()->id_role)
                ->get();
            foreach ($results as $value) {
                array_push($permissions, $value->access_code);
            }

            return response()->json([
                'success'   => true,
                'message'   => '',
                'data'      => [
                    'menu'  => [
                        'name'  => $menu->menu,
                        'link'  => $menu->link,
                        'permissions'   => $permissions,
                    ],
                ],
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success'   => false,
                'message'   => $th->getMessage(),
                'data'      => [],
            ], 500);
        }
    }
}
