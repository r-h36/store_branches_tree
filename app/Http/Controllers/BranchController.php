<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

use App\Branch;

class BranchController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * create the branch info
     * 
     * @param $request the request body sent over
     * @return json response whether the branch was created
     */
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

        try {
            $branch = new Branch();
            $branch->branch_name = $request->input('branch_name');
            $branch->save();
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Branch was created.',
        ], 201);
    }

    /**
     * update the branch info
     * 
     * @param $request the request body sent over
     * @param $id the branch id
     */
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

        if ($existingBranch->deleteTree()) {
            return response()->json([], 204);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Server error, can\'t delete the branch',
        ], 500);
    }

    /**
     * The function returns the branch node itself without any children
     */
    public function view(Request $request, int $id)
    {
        $existingBranch = Branch::where('id', $id)->first();
        if (empty($existingBranch)) {
            return response()->json([
                'status' => 'error',
                'message' => 'The branch does not exist.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Branch found.',
            'data' => (array)$existingBranch,
        ], 200);
    }

    /**
     * The function returns the branch and all of it children as in tree list
     */
    public function viewWithChildren(Request $request, int $id)
    {
        $existingBranch = Branch::where('id', $id)->first();
        if (empty($existingBranch)) {
            return response()->json([
                'status' => 'error',
                'message' => 'The branch does not exist.'
            ], 404);
        }

        $treeStartsWithTheBranch = $existingBranch->getAllChildrenBranches();

        return response()->json([
            'status' => 'success',
            'message' => 'Branch found.',
            'data' => $treeStartsWithTheBranch,
        ], 200);
    }

    public function index(Request $request)
    {
        $roots = Branch::where('parent_id', 0)->get();

        $allTrees = [];

        foreach ($roots as $key => $treeRoot) {
            $oneTree = $treeRoot->getAllChildrenBranches();
            array_push($allTrees, $oneTree);
        }

        return response()->json([
            'status' => 'success',
            'data' => $allTrees,
        ], 200);
    }

    public function move(Request $request, int $id, int $toBranchId)
    {
        if ($id === $toBranchId) {
            return response()->json([
                'status' => 'error',
                'message' => 'The branch to move and the parent can\'t be the same node',
            ], 422);
        }

        $branchToMove = Branch::where('id', $id)->first();
        if (empty($branchToMove)) {
            return response()->json([
                'status' => 'error',
                'message' => 'The branch to move does not exist. '
            ], 404);
        }

        $newParent = Branch::where('id', $toBranchId)->first();
        if (empty($newParent)) {
            return response()->json([
                'status' => 'error',
                'message' => 'The new parent branch does not exist.'
            ], 404);
        }

        if ($branchToMove->isAncestorOf($newParent)) {
            return response()->json([
                'status' => 'error',
                'message' => 'The new parent branch is a descendant of the branch to move. This move will create circle'
            ], 422);
        }


        DB::beginTransaction();
        try {
            DB::table('branches')->where('id', $id)->update(['parent_id' => $toBranchId]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'status' => 'error',
                'message' => 'Server error, can\'t move branch to new parent node',
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Branch has been moved to new parent',
        ], 200);
    }


}
