<?php

namespace App\Http\Controllers\Auth;



use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Star;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Repositories\FavoriteRepository;
use App\Http\Controllers\FileController as FileController;

class AuthController extends FileController
{

    protected $favoriteRepository;

    public function __construct(FavoriteRepository $favoriteRepository)
    {
        $this->favoriteRepository = $favoriteRepository;
    }


    public function registerUser(Request $request)
    {

        $validator = Validator::make(
            $request->all(),
            [
                'firstname' => 'required|string|max:255',
                'lastname' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6',
                'phoneOne' => 'required|string|regex:/^09[0-9]{8}$/',
                'phoneTwo' => 'required|string|regex:/^09[0-9]{8}$/',
                'stars' => 'required|integer',
            ]
        );
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);

        }
        $profileImage = $this->saveFile($request, 'profileImage', public_path('/uploads'));
        $user = User::create([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'profileImage' => $profileImage
        ]);

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        $star = Star::where('number', $request->stars)->first();

        if (!$star) {
            return response()->json(['message' => 'No offices found for the given number of stars'], 404);
        }
        ///choose favorite 
        $data =
            [
                'user_id' => $user->id,
                'star_id' => $star->id,
            ];

        $this->favoriteRepository->create($data);

        return response()->json([
            'user' => $user,
            'message' => 'ok',
            'token' => $token
        ]);
    }




    public function loginAdminAndUser(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        // Check email
        $user = User::where('email', $fields['email'])->first();

        // Check password
        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response([
                'message' => 'Password is worng or email'
            ], 401);
        }
        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }






}