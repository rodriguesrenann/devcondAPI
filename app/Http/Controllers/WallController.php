<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Wall;
use App\Models\WallLike;

class WallController extends Controller
{
    public function getAll()
    {
        $array = ['error' => ''];

        $user = Auth::user();
        $walls = Wall::select(['id', 'title', 'body', 'date_created'])->orderBy('date_created', 'DESC')->get();

        foreach ($walls as $wallKey => $wallValue) {
            $walls[$wallKey]['liked'] = false;
            $walls[$wallKey]['likes'] = 0;

            $likes = WallLike::where('id_wall', $wallValue['id'])->count();
            $walls[$wallKey]['likes'] = $likes;

            $isLiked = WallLike::where('id_wall', $wallValue['id'])->where('id_user', $user->id)->first();
            if ($isLiked) {
                $walls[$wallKey]['liked'] = true;
            }
        }
        $array['list'] = $walls;

        return $array;
    }

    public function toggleLike($id)
    {
        $array = ['error' => ''];

        $user = Auth::user();
        $wall = Wall::where('id', $id)->first();

        if (!$wall) {

            $array['error'] = 'Aviso não encontrado no sistema!';
            return $array;
        }

        $isLiked = WallLike::where('id_wall', $id)->where('id_user', $user->id)->first();

        if ($isLiked) {
            $isLiked->delete();

            $array['liked'] = false;
        } else {
            $newWallLike = new WallLike();
            $newWallLike->id_wall = $id;
            $newWallLike->id_user = $user->id;
            $newWallLike->save();

            $array['liked'] = true;
        }

        $array['likes'] = WallLike::where('id_wall', $id)->count();
        return $array;
    }
}
