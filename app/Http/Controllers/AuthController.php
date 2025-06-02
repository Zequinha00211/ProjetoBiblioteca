<?php

namespace App\Http\Controllers;

use App\Models\User as ModelsUser;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Foundation\Auth\User as AuthUser;
use Illuminate\Routing\Controller;
use Tymon\JWTAuth\Facades\JWTAuth;
use Validator;


class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/auth/login",
     *     tags={"Users"},
     *     @OA\RequestBody(
     *        @OA\JsonContent(
     *          type="object",
     *          @OA\Property(property="email", type="string", description="E-mail do usuário"),
     *          @OA\Property(property="password", type="string", description="Senha de usuário")
     *        )
     *     ),
     * 
     *     @OA\Response(response="200", description="An example resource"),
     * )
     */
    public function login(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        $credentials = $request->only('email', 'password');

        $user = User::where('email', 'like', $request->input('email'))->with('cmsPrivilege')->first();
       if (!$user) {
            return response()->json(['error' => 'invalid_credentials'], 400);
        }

        if (!\Hash::check($credentials['password'], $user->password)) {
            return response()->json(['error' => 'invalid_credentials'], 400);
        }

        try {
            $token = JWTAuth::customClaims([
                'group' => $user->cmsPrivilege->NOME
            ])->fromUser($user);

            return response()->json(compact('token'), 202);
        } catch (JWTException $e) {
            return response()->json(['error' => 'could_not_create_token'], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/auth/update",
     *     tags={"Users"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *        @OA\JsonContent(
     *          type="object",
     *          @OA\Property(property="id", type="string", description="ID do usuário"),
     *          @OA\Property(property="name", type="string", description="Nome do usuário"),
     *          @OA\Property(property="password", type="string", description="Senha de usuário")
     *        )
     *     ),
     * 
     *     @OA\Response(response="200", description="Atualização do usuário"),
     * )
     */

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
            'name' => 'required|string|between:2,100',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = User::where('id', $request->id)->update(
            [
                'password' => bcrypt($request->password),
                'name' => $request->name
            ]
        );

        return response()->json([
            'success' => true,
            'data' =>   $user
        ], 200);
    }


    /**
     * @OA\Post(
     *     path="/api/auth/register",
     *     tags={"Users"},
     *     @OA\RequestBody(
     *        @OA\JsonContent(
     *          type="object",
     *          @OA\Property(property="name", type="string", description="Nome do usuário"),
     *          @OA\Property(property="email", type="string", description="E-mail do usuário"),
     *          @OA\Property(property="password", type="string", description="Senha de usuário")
     *        )
     *     ),
     * 
     *     @OA\Response(response="200", description="An example resource"),
     * )
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = User::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password),
            'privilegio_id' => $request->privilegio_id]
        ));

        return response()->json([
            'success' => true,
            'data' =>   $user
        ], 201);
    }


    /**
     * @OA\Post(
     *     path="/api/auth/logout",
     *     tags={"Users"},
     *     @OA\RequestBody(
     *        @OA\JsonContent(
     *          type="object",
     *        )
     *     ),
     * 
     *     @OA\Response(response="200", description="An example resource"),
     * )
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'User successfully signed out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->createNewToken(auth()->refresh());
    }

    /**
     * @OA\Get(
     *     path="/api/auth/user-profile",
     *     description="Busca informações do usuári autenticado",
     *     security={{"bearerAuth":{}}},
     *     tags={"Users"},
     *     @OA\Response(response="200", description="An example resource",
     *       @OA\JsonContent(
     *          @OA\Property(property="email", type="string", description="E-mail do usuário"),
     *       ),
     *     ),
     *     @OA\Parameter(in="query", name="teste", description="string")
     * )
     */
    public function userProfile()
    {
        return response()->json(['success' => true, 'data' => auth()->user()]);
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token)
    {
        return response()->json([
            "success" => true,
            'data' => [
                'access_token' => $token,
                'expires_in' => auth('api')->factory()->getTTL() * 60,
                /*  'token_type' => 'bearer', */
                /* 'user' => auth()->user() */
            ]
        ]);
    }

    public function users()
    {
        try {
            $users = User::select([
                'id',
                'email',
                'name',
            ])->get();
            return response()->json(['sucesso' => true, 'data' => $users]);
        } catch (\Exception $e) {
            return response()->json(['sucesso' => false, 'erros' => [$e->getMessage()]], 500);
        }
    }

    public function keyCloackLogin(Request $request)
    {
        $output = new \Symfony\Component\Console\Output\ConsoleOutput();
        try {
            $validator = \Validator::make($request->all(), [
                'username' => 'required',
                'password' => 'required|string|min:6',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }
            $userName = $request->username;
            $cacheName = "token_user_$userName";
            $hasCache = \Cache::has($cacheName);
            $token = null;
            $hasToken = false;
            if ($hasCache) {
                $token = \Cache::get($cacheName);
                $explode = explode(".", $token);
                $payload = json_decode(base64_decode($explode[1]));
                $date = date('Y-m-d H:i:s', $payload->exp);
                $hasToken = (bool) (new \DateTime(strval($date)) > new \DateTime());
            }

            if (!$hasToken) {
                $keycloak = new Keycloak();
                $response = $keycloak->login($request->all());
                $token = $response->access_token;
                $hourEmSegundos = 60 * 60;
                $seconds = $hourEmSegundos * 8;
                \Cache::put($cacheName, $token, $seconds);
                $output->writeln("<info>keyCloackLogin: Autenticando</info>");
            }
            return response()->json(['access_token' =>  $token]);
        } catch (\Exception $e) {
            $codeStatus = $e->getCode() != 0 ?  $e->getCode() : 500;

            $message = 'IP:' . $request->ip() . -' Message: ' . $e->getMessage() . ' CodeStatus: ' . $codeStatus;
            $output->writeln("<info>keyCloackLogin: $message</info>");

            return response()->json([
                'sucesso' => false,
                'status' => $e->getMessage()
            ], $codeStatus);
        }
    }
}
