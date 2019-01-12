<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Branch;

class BranchController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('api');
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'branch_name' => 'required|unique:branches|max:255',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();

            return response()->json([
                'status' => 'error',
                'message' => $errors->first('branch_name'),
            ], 400);
        }

        $branch = new Branch();
        $branch->branch_name = $request->input('branch_name');
        if ($branch->save()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Branch was created.',
            ], 200);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Server error, can\'t create new branch',
        ], 500);
    }

    public function update(Request $request, int $id)
    {

        $existingBranch = Branch::where('id', $id)->first();
        if (empty($existingBranch)) {
            return response()->json([
                'status' => 'error',
                'message' => 'The branch does not exist.'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'branch_name' => 'required|unique:branches|max:255',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();

            return response()->json([
                'status' => 'error',
                'message' => $errors->first('branch_name'),
            ], 400);
        }

        $existingBranch->branch_name = $request->input('branch_name');
        if ($existingBranch->save()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Branch was created.',
            ], 200);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Server error, can\'t update branch info',
        ], 500);

    }

    public function delete(Request $request, int $id)
    {
        $existingBranch = Branch::where('id', $id)->first();
        if (empty($existingBranch)) {
            return response()->json([
                'status' => 'error',
                'message' => 'The branch does not exist.'
            ], 404);
        }

        if ($existingBranch->delete()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Branch was created.',
            ], 200);
        }


        return response()->json([
            'status' => 'error',
            'message' => 'Server error, can\'t delete the branch',
        ], 500);
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
