<?php

namespace App\View\Components;

use Illuminate\View\Component;

class BalanceCard extends Component
{
    public $balance;

    public function __construct($balance)
    {
        $this->balance = $balance;
    }

    public function render()
    {
        return view('components.balance-card');
    }
}