<?php

use Illuminate\Support\Facades\Auth;
use App\Actions\Fortify\UpdateUserPassword;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Kata Sandi')] class extends Component {
    public $current_password;
    public $password;
    public $password_confirmation;

    public function updatePassword(UpdateUserPassword $updater)
    {
        $updater->update(Auth::user(), [
            'current_password' => $this->current_password,
            'password' => $this->password,
            'password_confirmation' => $this->password_confirmation,
        ]);

        $this->reset();
        $this->resetErrorBag();
        $this->resetValidation();
    }
};
