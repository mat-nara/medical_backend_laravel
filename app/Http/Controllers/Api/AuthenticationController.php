<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\Exceptions\MissingAbilityException;
use LogActivity;

class AuthenticationController extends Controller
{
    /**
     * Authenticate an user and dispatch token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request) {
        $creds = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $creds['email'])->first();
        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response(['error' => 1, 'message' => "Vos informations d'identification sont invalides"], 401);
        }

        if (config('personnal.delete_previous_access_tokens_on_login', false)) {
            $user->tokens()->delete();
        }

        $roles = $user->roles->pluck('slug')->all();

        $plainTextToken = $user->createToken('personnal-api-token', $roles)->plainTextToken;

        LogActivity::addToLog($user->name . ' logged in');
        return response(['error' => 0, 'id' => $user->id, 'token' => $plainTextToken, 'userInfo' => $user], 200);
    }

    /**
     * Return Authenticated user associate with request token
     *
     * @param  Request  $request
     * @return mixed
     */
    public function authenticated_user(Request $request) {
        return $request->user();
    }

    /**
     * Revoke token; only remove token that is used to perform logout (i.e. will not revoke all tokens)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
	public function logout(Request $request) {
        
		// Revoke the token that was used to authenticate the current request
		$request->user()->currentAccessToken()->delete();
		//$request->user->tokens()->delete(); // use this to revoke all tokens (logout from all devices)

        LogActivity::addToLog($request->user()->name . ' had been logged out');
		return response(['message'=>'Successully disconnected'], 200);
	}

    /**
     * Send password reset link to the sent email adress
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sendPasswordResetLinkEmail(Request $request) {
		$request->validate(['email' => 'required|email']);

		$status = Password::sendResetLink(
			$request->only('email')
		);

		if($status === Password::RESET_LINK_SENT) {
			return response()->json(['message' => __($status)], 200);
		} else {
			throw ValidationException::withMessages([
				'email' => __($status)
			]);
		}
	}

    /**
     * Reset password
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
	public function resetPassword(Request $request) 
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);
     
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));
     
                $user->save();
     
                event(new PasswordReset($user));
            }
        );

        if($status === Password::PASSWORD_RESET) {
			return response()->json(['message' => 'Mot de passe reinitialiser avec success!'], 200);
		} else {
			throw ValidationException::withMessages([
				'email' => __($status)
			]);
		}
    }

}
