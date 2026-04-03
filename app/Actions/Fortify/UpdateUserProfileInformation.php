<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    /**
     * Validate and update the given user's profile information.
     *
     * @param  array<string, string>  $input
     */
    public function update(User $user, array $input): void
    {
        Validator::make(
            $input,
            [
                'fullname' => ['required', 'string', 'max:255'],
                'employee_code' => ['required', 'string', 'max:255'],
            ]
        )->validateWithBag('updateProfileInformation');

        $user->employee()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'fullname' => ucwords(strtolower($input['fullname'])),
                'employee_code' => $input['employee_code'],
            ]
        );
    }
}
