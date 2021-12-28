<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegistrationRequest;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'registration', 'refresh']]);
    }

    /**
     * Get a JWT via given credentials.
     * @param LoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request)
    {
//        $credentials = request(['name', 'email', 'password']);
        $credentials = $request->toArray();

        if (! $token = auth()->attempt($credentials)) {
        
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * User registration
     * @param RegistrationRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function registration(RegistrationRequest $request)
    {
//        try {
            $credentials = $request->toArray();
            $user = new User();
            $user->username = $credentials['username'];
            $user->name = $credentials['name'];
            $user->email = $credentials['email'];
            $user->password = Hash::make($credentials['password']);
            $user->save();

            return response()->json(['message' => 'Successfully registration!', 'code' => 0]);
//        }catch (\Exception $exception){
//            return response($exception->getMessage());
//        }
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {                
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
  public function hello()   {
      return 'Hello';
  }
}