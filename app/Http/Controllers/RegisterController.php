<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Mail\VerificationPIN;
use App\Models\User;
use App\Models\UserEmailVerification;
use App\Models\UserPinVerfication;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use stdClass;
use Symfony\Component\HttpFoundation\Request;

class RegisterController extends Controller
{
    const USER = 1;
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_name' => 'required|max:20|min:4',
            'password' => 'required',
            'confirm_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }
        $email_find = UserEmailVerification::where('email_code', $request->code)->first();
        if ($email_find == null) {
            return $this->sendError("invalid request please check link on your email");
        }

        $user_find = User::where('email', $email_find->email)->first();
        if ($user_find != null) {
            return $this->sendError("Email already registered");
        }

        $send_pin = $this->SendPIN($email_find);
        if ($send_pin == true) {
            $input = $request->all();
            $data = [
                'name' => $input['user_name'],
                'password' => bcrypt($input['password']),
                'user_role' => SELF::USER,
                'email' => $email_find->email,
                'registered_at' => Carbon::now(),
            ];
            $user = User::create($data);
            return $this->sendResponse(new stdClass(), "verification pin successfully send please check your email");
        } else {
            return $this->sendError("oops something went wrong please try again later");
        }

    }

    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        if (\Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = \Auth::user();
            $user['token'] = $user->createToken('MyApp')->accessToken;
            return new UserResource($user);
        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised']);
        }
    }

    public function sendInvitaionLink(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }

        $random = Str::random(6);
        $details = [
            'title' => 'Invitation link',
            'body' => URL::to('/') . "/api/register/" . $random,
        ];

        try {
            \Mail::to($request->email)->send(new \App\Mail\SendInvitation($details));
            $data = [
                'email' => $request->email,
                'email_code' => $random,
            ];

            UserEmailVerification::create($data);
        } catch (\Exception $e) {

            return response()->json([
                "data" => new stdClass(),
                "message" => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json([
            "data" => new stdClass(),
            "message" => "please check our email link send successfully",
        ], Response::HTTP_OK);

    }
    public function SendPIN($request)
    {

        $pin = random_int(100000, 999999);

        $details = [
            'title' => 'Verfication PIN',
            'body' => $pin,
        ];

        try {
            \Mail::to($request->email)->send(new \App\Mail\VerificationPIN($details));
            $data = [
                'user_email_verifications_id' => $request->id,
                'send_pin' => $pin,
            ];
            UserPinVerfication::create($data);
        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            return false;
        }

        return true;
    }
    public function PINVerifying(Request $request)
    {
        $verfiying_pin = UserPinVerfication::where('send_pin', $request->pin)->with('useremailverifications')->first();
        if ($verfiying_pin == null) {
            return $this->sendError("invalid pin please check your email");
        }
        $user = User::where('email', $verfiying_pin->useremailverifications->email)->first();
        if ($user == null) {
            return $this->sendError("oops something went wrong please try again later");
        }

        $user['token'] = $user->createToken('MyApp')->accessToken;
        return new UserResource($user);
    }
}
