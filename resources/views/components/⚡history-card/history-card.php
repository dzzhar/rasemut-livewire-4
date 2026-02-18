<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component {
    public string $headerTitle;

    public $filterDate = null;
    public $min;
    public $max;

    #[Locked]
    public string $model;
    #[Locked]
    public string $dateColumn;
    #[Locked]
    public array $select = [];

    public function mount()
    {
        $this->min = now()->startOfMonth()->toDateString();
        $this->max = now()->endOfMonth()->toDateString();
    }

    #[Computed]
    public function history()
    {
        $employee = Auth::user()?->employee;
        if (!$employee) {
            return collect();
        }

        $query = $this->model::whereBelongsTo($employee)
            ->select($this->select);

        // cek apakah ada filter tanggal
        if ($this->filterDate) {
            $query->whereDate($this->dateColumn, $this->filterDate);
        } else {
            // default tanggal history
            $query->whereMonth($this->dateColumn, now()->month)->whereYear($this->dateColumn, now()->year);
        }

        return $query->latest($this->dateColumn)->get();
    }

    #[On('refresh-history')]
    public function refreshHistory() {}

    public function showModal($id)
    {
        $this->dispatch('show-detail-history', id: $id);
    }
};
