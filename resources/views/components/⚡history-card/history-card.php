<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component {
    public string $headerTitle;

    public int $perPage = 1;
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

        $query = $this->model::whereBelongsTo($employee)->select($this->select);

        // cek apakah ada filter tanggal
        if ($this->filterDate) {
            $query->whereDate($this->dateColumn, $this->filterDate);
        } else {
            // default tanggal history
            $query->whereBetween($this->dateColumn, [now()->startOfMonth(), now()->endOfMonth()]);
        }

        return $query->latest($this->dateColumn)->limit($this->perPage)->get();
    }

    #[Computed]
    public function totalHistory()
    {
        $employee = Auth::user()?->employee;
        if (!$employee) {
            return 0;
        }

        $query = $this->model::whereBelongsTo($employee);

        if ($this->filterDate) {
            $query->whereDate($this->dateColumn, $this->filterDate);
        } else {
            $query->whereBetween($this->dateColumn, [now()->startOfMonth(), now()->endOfMonth()]);
        }

        return $query->count();
    }

    public function loadMore()
    {
        $this->perPage += 10;
    }

    public function updatedFilterDate()
    {
        $this->perPage = 10;
    }

    #[On('refresh-history')]
    public function refreshHistory() {}

    public function showModal($id)
    {
        $this->dispatch('show-detail-history', id: $id);
    }
};
