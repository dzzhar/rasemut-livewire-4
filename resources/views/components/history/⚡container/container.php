<?php

use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;

new class extends Component {
    public int $employeeId = 1;
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

    #[Computed]
    public function history()
    {
        $model = $this->model;
        $query = $model::where('employee_id', $this->employeeId)->select($this->select);

        // cek apakah ada filter tanggal
        if ($this->filterDate) {
            $query->whereDate($this->dateColumn, $this->filterDate);
        } else {
            // default tanggal history
            $query->whereMonth($this->dateColumn, now()->month)->whereYear($this->dateColumn, now()->year);
        }

        return $query->latest($this->dateColumn)->get();
    }

    public function mount()
    {
        $this->min = now()->startOfMonth()->toDateString();
        $this->max = now()->endOfMonth()->toDateString();
    }
};
