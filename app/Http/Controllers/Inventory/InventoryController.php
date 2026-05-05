<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;

class InventoryController extends Controller
{
    public function index()
    {
        return view('inventory.index');
    }

    public function trash()
    {
        return view('inventory.trash');
    }
}
