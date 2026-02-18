<?php

use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Auth;
use App\Actions\Fortify\UpdateUserPassword;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Kata Sandi')] class extends Component {
    public $current_password;
    public $password;
    public $password_confirmation;

    public function updatePassword(UpdateUserPassword $updater)
    {
        $this->validate();

        // cek password baru tidak boleh sama dengan password lama
        if (Hash::check($this->password, Auth::user()->password)) {
            $this->addError('password', 'Kata sandi baru tidak boleh sama dengan kata sandi lama.');
            return;
        }

        $updater->update(Auth::user(), [
            'current_password' => $this->current_password,
            'password' => $this->password,
            'password_confirmation' => $this->password_confirmation,
        ]);

        $this->reset();
    }

    protected function rules()
    {
        return [
            'current_password' => ['required'],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(),
            ],
            'password_confirmation' => ['required', 'string', 'min:8'],
        ];
    }
};
