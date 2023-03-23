<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\UsersLoginRequest;
use App\User;
use Mail;
use Hash;

class AuthController extends Controller
{
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(UsersLoginRequest $request)
    {
        $credentials = ['email' => $request->dados['email'], 'password' => $request->dados['password']];

        if (!$token = auth()->attempt($credentials))
            return response()->json(['success' => false, 'errors' => 'Unauthorized']);

        //verificando se o usuário é administrador
        $userData = User::select("admin", "name", "email")->where("email", $request->dados['email'])->first();

        return response()->json([
            'success'       => true,
            'access_token'  => $token,
            'isAdmin'       => $userData->admin,
            'name'          => $userData->name,
            'email'         => $userData->email,
            'token_type'    => 'bearer',
            'expires_in'    => auth()->factory()->getTTL() * 360
        ]);
        //return $this->respondWithToken($token, $isAdmin);
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

        return response()->json(["success" => true]);
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
            'success'       => true,
            'access_token'  => $token,
            'token_type'    => 'bearer',
            'expires_in'    => auth()->factory()->getTTL() * 60
        ]);
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\Guard
     */
    public function guard()
    {
        return Auth::guard();
    }


    public function forgotPassword(Request $request)
    {
        $email = $request->email;
        $changeUsername = $request->changeUsername;
        $oldUsername = $request->oldUsername;
        $numRand = rand(1000, 9999);
        $token = Hash::make($numRand);

        if ($changeUsername){
            $userData = User::where("email", $oldUsername)->first();
            $updateUser = User::where("email", $oldUsername)->update(["email" => $email, "remember_token" => $token]);
            if ($updateUser){
                Mail::send("forgotPassword.email", [ "data" => $userData, "cod" => $numRand ], function ($message) use ($email) {
                    $message->from("contato@quizvicentino.com.br")
                            ->to($email, "Quiz Vicentino")
                            ->subject("Recuperar senha - Quiz Vicentino");
                });

                return response()->json(["success" => true]);
            }
        }else{
            $userData = User::where("email", $email)->first();
            if (!is_null($userData)){
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
                    return response()->json(["success" => true, "changeUsername" => true]);
                }else{
                    $updateTokenUser = User::where("email", $email)->update(["remember_token" => $token]);
                    if ($updateTokenUser){
                        Mail::send("forgotPassword.email", [ "data" => $userData, "cod" => $numRand ], function ($message) use ($email) {
                            $message->from("contato@quizvicentino.com.br")
                                    ->to($email, "Quiz Vicentino")
                                    ->subject("Recuperar senha - Quiz Vicentino");
                        });
                    
                        return response()->json(["success" => true]);
                    }
                }
            }else{
                return response()->json(["success" => false, "msg" => "Usuário não encontrado"]);
            }
        }

    }

    public function checkSecurityCode(Request $request)
    {
        $securityCode = strval($request->securityCode);
        $email = $request->email;
        
        if ($securityCode == 0)
        return response()->json(["success" => false, "msg" => "Erro ao validar o código de segurança"]);
        
        $rememberToken = User::select("remember_token")->where("email", $email)->first()->remember_token;
        if (!Hash::check($securityCode, $rememberToken))
            return response()->json(["success" => false, "msg" => "Código de segurança inválido"]);

        return response()->json(["success" => true]);
    }

    public function resetPassword(Request $request)
    {        
        $newPassword = $request->password;
        $email = $request->email;
        User::where("email", $email)->update(["password" => Hash::make($newPassword)]);

        return response()->json(["success" => true]);
    }
}
