<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Torneo; 

class TableController extends Controller
{
    public function getTorneos()
    {
        return datatables()->of(Torneo::all())->make(true);
    }
}
