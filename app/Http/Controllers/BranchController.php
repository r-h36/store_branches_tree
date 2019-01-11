<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BranchController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('api');
    }

    public function create(Request $request)
    {

    }

    public function update(Request $request, int $id)
    {

    }

    public function delete(Request $request, int $id)
    {

    }

    public function view(Request $request, int $id)
    {

    }

    public function viewAll(Request $request)
    {

    }

    public function move(Request $request, int $id, int $toBranchId)
    {

    }

    public function viewWithoutChildren(Request $request, int $id)
    {

    }
}
