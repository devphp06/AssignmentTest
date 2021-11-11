<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUser;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{

    public function profile_Update(UpdateUser $request)
    {

        if ($request->header('Authorization')) {
            $user = Auth::user();
            if ($request->has('avatar')) {
                $allowedfileExtension = ['pdf', 'jpg', 'png'];
                $file = $request->avatar;
                $extension = $file->getClientOriginalExtension();
                $check = in_array($extension, $allowedfileExtension);
                if ($check) {
                    $mediaFiles = $request->avatar;
                    $path = $mediaFiles->store('public');
                    $name = $mediaFiles->getClientOriginalName();
                    //store image file into directory and db
                    $user->name = $request->user_name;
                    $user->avatar = "storage/api/" . $path;
                    $user->save();
                } else {
                    return $this->sendResponse(new stdClass(), "Invalid Format");
                }
            }

            $user['token'] = $user->createToken('MyApp')->accessToken;
            return new UserResource($user);
        }
        return $this->sendError('Unauthorised.', "");

    }

}
