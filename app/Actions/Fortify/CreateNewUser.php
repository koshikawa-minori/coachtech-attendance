<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Http\Requests\FortifyRegisterRequest;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    public function create(array $input): User
    {
        /** @var FortifyRegisterRequest $registerRequest */
        $registerRequest = app(FortifyRegisterRequest::class);

        Validator::make(
            $input,
            $registerRequest->rules(),
            $registerRequest->messages()
        )->validate();

        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ]);

        event(new Registered($user));

        return $user;
    }
}
