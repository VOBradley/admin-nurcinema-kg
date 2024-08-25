<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\UserLoginRequest;
use App\Http\Requests\ConfirmCreateUserRequest;
use App\Http\Requests\ConfirmRegisterRequest;
use App\Http\Requests\RegisterRequest;
use App\Mail\OtpMail;
use App\Models\User;
use App\Models\UserOtp;
use App\ResponseService;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Random\RandomException;

class UserController extends Controller
{
    public function remove(Request $request): \Illuminate\Foundation\Application|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        $id = $request->post('id');
        var_dump($request->all());

        User::whereId($id)->firstOrFail()->delete();

        return redirect(route('register', absolute: false));
    }

    /**
     * @throws RandomException
     */
    public function registration(RegisterRequest $request)
    {
        $email = $request->post('email');

        if (is_null($email)) {
            return ResponseService::success([
                'email' => $email,
            ], 'Email не введёт');
        }

        $user = User::whereEmail($email)->first();
        if (isset($user)) {
            return ResponseService::success([
                'email' => $email,
            ], 'Email адрес занят');
        }


// Проверка наличия записи с тем же email и созданной в течение последних 5 минут
        $recentOtp = UserOtp::where('otp_model', $email)
            ->where('created_at', '>=', Carbon::now()->subMinutes(5))
            ->first();

        if ($recentOtp) {
            return ResponseService::success([
                'otp_time' => $recentOtp->created_at->subMinutes(5),
            ], 'OTP код уже отправлен!');
        }

// Удаление всех старых записей OTP для данного email
        UserOtp::where('otp_model', $email)->delete();

// Генерация нового OTP-кода и временного ключа
        $randomNumber = random_int(100001, 999999);
        $otpTemp = Str::uuid();

// Отправка email с новым OTP-кодом
        $mail = Mail::to($email)->send(new OtpMail($randomNumber));

// Сохранение нового OTP-кода в базе данных
        UserOtp::create([
            'otp_code' => $randomNumber,
            'otp_model' => $email,
            'otp_temp' => $otpTemp,
            'otp_type' => 'REGISTER'
        ]);
        return ResponseService::success([
            'otp_temp' => $otpTemp,
        ]);
    }

    public function confirmRegisterOtp(ConfirmRegisterRequest $request)
    {
        $email = $request->post('email');
        $otp_code = $request->post('otp_code');
        $otp_temp = $request->post('otp_temp');

        $user = User::whereEmail($email)->first();
        if (isset($user)) {
            return [
                'status' => 400,
                'message' => 'Email адрес занят',
                'response' => $email
            ];
        }


        $otpAttempt = UserOtp::whereOtpTemp($otp_temp)->first();
        if ($otpAttempt?->otp_attempt > 2) {
            return [
                'status' => 400,
                'message' => 'Вы потратили все свои попытки!',
                'response' => $otp_code
            ];
        }
        $otp = $otpAttempt->where([
            'otp_code' => $otp_code,
            'otp_model' => $email,
            'otp_type' => 'REGISTER'
        ])->first();
        if (is_null($otp)) {
            $otpAttempt->otp_attempt = $otpAttempt->otp_attempt + 1;
            $otpAttempt->save();
            return [
                'status' => 400,
                'message' => 'Не верный код!',
                'response' => $otp_code
            ];
        }
        $otpAttempt->delete();

        $randomNumber = random_int(100001, 999999);
        $newOtpTemp = Str::uuid();

        UserOtp::create([
            'otp_code' => $randomNumber,
            'otp_model' => $email,
            'otp_temp' => $newOtpTemp,
            'otp_type' => 'CONFIRM_REGISTER_CODE'
        ]);

        return ResponseService::success([
            'otp_temp' => $newOtpTemp,
            'otp_code' => $randomNumber
        ]);
    }

    public function createAccount(ConfirmCreateUserRequest $request)
    {
        $email = $request->post('email');
        $otp_code = $request->post('otp_code');
        $otp_temp = $request->post('otp_temp');
        $password = $request->post('password');
        $name = $request->post('name');

        $user = User::whereEmail($email)->first();
        if (isset($user)) {
            return [
                'status' => 400,
                'message' => 'Email адрес занят',
                'response' => $email
            ];
        }

        $otp = UserOtp::where([
            'otp_temp' => $otp_temp,
            'otp_code' => $otp_code,
            'otp_model' => $email,
            'otp_type' => 'CONFIRM_REGISTER_CODE'
        ])->first();

        if (is_null($otp)) {
            return [
                'status' => 400,
                'message' => 'Не верный код!',
                'response' => $otp_code
            ];
        }

        $newUser = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'email_verified_at' => Carbon::now(),
            'is_admin' => false
        ]);

        event(new Registered($user));

        $token = $newUser->createToken("vshchukin")->plainTextToken;
        $otp->delete();
        return ResponseService::success([
            'email' => $email,
            'token' => $token
        ]);
    }

    public function login(UserLoginRequest $request)
    {
        if (!Auth::attempt($request->only(['email', 'password']))) {
            return ResponseService::fail([
                'token' => null
            ], 'Не верный логин или пароль');
        }

        $user = User::where('email', $request->email)->first();
        return ResponseService::success([
            'token' => $user->createToken("API TOKEN")->plainTextToken
        ]);
    }
}
