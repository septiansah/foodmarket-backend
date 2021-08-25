<?php

namespace App\Http\Controllers\API;

use App\Actions\Fortify\PasswordValidationRules;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
// use PharIo\Manifest\Email;

class UserController extends Controller
{
    //
    use PasswordValidationRules;

    public function login(Request $request) {

        try {
            //validation input
            $request->validate([
                'email' => 'email|required',
                'password' => 'required'
            ]);

            //check credentials
            $credentials = request(['email', 'password']);
            if (!Auth::attempt($credentials)) {
                return ResponseFormatter::error([
                    'message' => 'Unauthorize'
                ], 'Authentication Failed', 500);
            }

            //check password users
            $user = User::where('email', $request->email)->first();
            if(!Hash::check($request->password, $user->password, [])){
                throw new \Exception('Invalid Credential');
            }

            //jika user berhasil login

            $tokenResult = $user->createToken('authToken')->plainTextToken;
            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user
            ], 'Authenticated');

        } catch(Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error
            ], 'Authentication Failed', 500);
        }
    }

    public function register(Request $request){

        try {
            $validator = Validator::make($request->all(), [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => $this->passwordRules()
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            };

            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'address' => $request->address,
                'houseNumber' => $request->houseNumber,
                'phoneNumber' => $request->phoneNumber,
                'city' => $request->city,
                'password' => Hash::make($request->password),
            ]);

            $user = User::where('email', $request->email)->first();

            $tokenResult = $user->createToken('authToken')->plainTextToken;

            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user
            ],'User Registered');

        } catch(Exception $error) {
            error_log('Some message here',1);
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error,
            ], 'Authentication failed', 500);
        }
    }


    // public function register(Request $request)
    // {
    //     try {
    //         $validator = Validator::make($request->all(), [
    //             'name' => ['required', 'string', 'max:255'],
    //             'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
    //             'password' => $this->passwordRules()
    //         ]);
    //         // error_log($validator);
    //         if ($validator->fails()) {
    //             return response()->json($validator->errors(), 400);
    //         };

    //         User::create([
    //             'name' => $request->name,
    //             'email' => $request->email,
    //             'address' => $request->address,
    //             'houseNumber' => $request->houseNumber,
    //             'phoneNumber' => $request->phoneNumber,
    //             'city' => $request->city,
    //             'password' => Hash::make($request->password),
    //         ]);

    //         $user = User::where('email', $request->email)->first();

    //         // $tokenResult = $user->createToken('authToken')->plainTextToken;

    //         return ResponseFormatter::success([
    //             // 'access_token' => $tokenResult,
    //             'token_type' => 'Bearer',
    //             'user' => $user
    //         ],'User Registered');
    //     } catch (Exception $error) {
    //         // error_log();
    //         return ResponseFormatter::error([
    //             'message' => 'Something went wrong',
    //             'error' => $error,
    //         ],'Authentication Failed', 500);
    //     }
    // }

    public function logout(Request $request) {
        $token = $request->user()->currentAccessToken()->delete();
        return ResponseFormatter::success([
            $token, 'Token Revoked'
        ]);
    }

    public function update(Request $request) {
        $data = $request->all();
        $user = Auth::user();
        $user->update($data);

        return ResponseFormatter::success([
            $user, 'Profile Update'
        ]);
    }

    public function fetch(Request $request){
        return ResponseFormatter::success([
            $request->user(), 'Data profile berhasil di ambil'
        ]);
    }

    public function updatePhoto(Request $request) {
        $validator = Validator::make($request->all(), [
            'file' => 'required|image|max:2048'
        ]);
        if ($validator->fails())
        {
            return ResponseFormatter::error([
                'error' => $validator->errors()
            ], 'Update photo fails', 401);
        }

        if($request->file('file'))
        {
            $file = $request->file->store('assets/user', 'public');
            $user = Auth::user();
            $user->profile_photo_path = $file;
            $user->update();

            return ResponseFormatter::success([
                [$file], 'File berhasil di upload'
            ]);
        }
    }
}
