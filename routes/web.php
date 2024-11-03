<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

Route::post('/bale_webhook', [\App\Http\Controllers\NotifBot::class,'bale']);
Route::post('/telegram_webhook', [\App\Http\Controllers\NotifBot::class,'telegram']);

Route::middleware('auth:sanctum')->get('/get-notifbot-code', function (Request $request) {
    do {
        $randomCode = Str::random(6);
    } while (User::where('notifbot_code', $randomCode)->exists());
    $user = $request->user();
    $user->notifbot_code = $randomCode;
    $user->save();
    return $randomCode;
});

Route::middleware('auth:sanctum')->get('/send-notif-tst',function (){
    $notif = new \App\Http\Controllers\NotifBot();
    $notif->state = 'bale';
    $notif->sendMessageByUser(\Illuminate\Support\Facades\Auth::user(),'اطلاعیه تست 1');

    return 'اطلاعیه ارسال شد';
});


Route::post('/login', function (Request $request) {
    $request->validate([
        'email' => 'required|string|email',
        'password' => 'required|string',
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.'],
        ]);
    }

    return $user->createToken('auth_token')->plainTextToken;
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/users', function () {
    return User::all();
});

