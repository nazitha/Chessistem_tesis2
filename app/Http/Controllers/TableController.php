<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Torneo; 
use Yajra\DataTables\Facades\DataTables;

/**
 * @method static \Yajra\DataTables\DataTables of($query)
 * @property-read \Yajra\DataTables\Facades\DataTables $DataTables
 * @phpstan-ignore-next-line
 */
class TableController extends Controller
{
    /**
     * Get torneos data for DataTables
     * 
     * @return \Illuminate\Http\JsonResponse
     * @phpstan-ignore-next-line
     */
    public function getTorneos()
    {
        return DataTables::of(Torneo::all())->make(true);
    }
}
