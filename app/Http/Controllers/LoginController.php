<?php
declare(strict_types=1);
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            return $this->jsonResponse([
                "status"  => 200,
                "user_id" => Auth::id(),
                "message" => "ログインに成功しました。"
            ]);
        } else {
            return $this->jsonResponse([
                "status"  => 401,
                "message" => "メールアドレスかパスワードが間違っています。"
            ], 401);
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        return response()->json([
            "status"  => 200,
            "message" => "ログアウトに成功しました。" // 元々ログインしていなくても表示される
        ]);
    }
}
