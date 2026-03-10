<?php

use Flux\Flux;
use Livewire\Component;
use Livewire\Attributes\On;

new class extends Component {
    public $type = 'success';
    public $title = '';
    public $message = '';

    #[On('show-feedback')]
    public function show($title, $message, $type = "success")
    {
        $this->title = $title;
        $this->message = $message;
        $this->type = $type;

        Flux::modal('feedback-modal')->show();
    }
};
