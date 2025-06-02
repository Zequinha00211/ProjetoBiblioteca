<?php

namespace App\Http\Controllers;

use App\Models\Privilegio;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Storage;
use Facades\App\Services\UserService;
use Tymon\JWTAuth\Facades\JWTAuth as FacadesJWTAuth;

class UserController
{
    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');

        return $user = User::where('email', 'like', $request->input('email'))->first();

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

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:cms_users',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json((array) $validator->errors()->messages(), 400);
        }

        $user = User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
        ]);

        return response()->json(compact('user'), 201);
    }

    public function update(Request $request)
    {
        try {
            $usuarioPersited = User::where('id', $request->id)->first();

            if (!$usuarioPersited) {
                return response()->json(['sucesso' => false, 'mensagem' => 'Usuário não encontrado'], 404);
            }

            if ($request->password) {
                $usuarioPersited->password = Hash::make($request->password);
            }

            $usuarioPersited->email         = $request->email;
            $usuarioPersited->name          = $request->name;
            $usuarioPersited->privilegio_id = isset($request->privilegio_id) ? $request->privilegio_id : null;
            $usuarioPersited->update();
            return response()->json([
                'sucesso' => true,
                'data' => $usuarioPersited->fresh()
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['sucesso' => false, 'erros' => [$e->getMessage(), $e->getLine(), $e->getFile()]], 500);
        }
    }

    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            return response()->json(['success' => true, 'message' => 'Logout successful'], 200);
        } catch (JWTException $e) {
            return response()->json(['success' => false, 'error' => 'Failed to logout, please try again.'], 500);
        }
    }

    public function todosUsuarios()
    {
        try {
            $usuarios = User::get();

            return response()->json(['sucesso' => true, 'data' => $usuarios]);
        } catch (\Exception $e) {
            return response()->json(['sucesso' => false, 'erros' => [$e->getMessage(), $e->getLine(), $e->getFile()]], 500);
        }
    }

    public function atualizarFotoUsuario(Request $request, $idUser)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'password' => 'required|string|min:6',
            ]);

            if ($validator->fails()) {
                return response()->json((array) $validator->errors()->messages(), 400);
            }

            if (!empty($request->file('foto'))) {
                UserService::uploadFotoPerfil($idUser, $request->file('foto'));
            }

            User::where('id', $idUser)->update([
                'name' => $request->input('name'),
                'password' => Hash::make($request->input('password')),
            ]);

            return response()->json(['sucesso' => true, 'data' => null]);
        } catch (\Exception $e) {
            return response()->json(['sucesso' => false, 'erros' => [$e->getMessage(), $e->getLine(), $e->getFile()]], 500);
        }
    }

    public function getImageRandomLogin()
    {
        $files = Storage::disk('imagens-login')->files();
        $file = $files[rand(0, count($files) - 1)];

        return response()->file(Storage::disk('imagens-login')->path('/') . $file);
    }
    public function buscarUsuario($idUsuario)
    {
        try {
            $usuario = User::where('id', $idUsuario)->first();

            return response()->json(['sucesso' => true, 'data' => $usuario]);
        } catch (\Exception $e) {
            return response()->json(['sucesso' => false, 'erros' => [$e->getMessage(), $e->getLine(), $e->getFile()]], 500);
        }
    }

    public function deletarUsuario($idUsuario)
    {
        try {
            User::where('id', $idUsuario)->delete();
            return response()->json(['sucesso' => true], 200);
        } catch (\Exception $e) {
            return response()->json(['sucesso' => false, 'erros' => [$e->getMessage()]], 500);
        }
    }

    public function buscarPrivilegios()
    {
        try {
            $privilegios = Privilegio::get();

            return response()->json(['sucesso' => true, 'data' => $privilegios]);
        } catch (\Exception $e) {
            return response()->json(['sucesso' => false, 'erros' => [$e->getMessage(), $e->getLine(), $e->getFile()]], 500);
        }
    }
    public function getUser()
    {


        $user = JwtAuth::parseToken()->authenticate();
        $user->privilegio = [$user->cmsPrivilege];

        return ['sucesso' => true, 'data' => $user];
    }

    public function atualizarProprioUser(Request $request)
    {
        $user = User::where('id', $request->id)->first();

        if(!empty($user)){
            if ($request->password) {
                $user->password = Hash::make($request->password);
            }
            if ($request->name) {
                $user->name = $request->name;
            }

            $user->update();
        }

        return response()->json([
            'success' => true,
            'data' =>   $user
        ], 200);
    }
}
