<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Branch extends Model
{
    //


    /**
     * function to delete all the children assign to this branch
     * 
     */
    public function deleteChildren()
    {
        $tree = $this->getAllChildrenBranches();

        $branchIds = [];
        foreach ($tree as $node) {
            array_push($branchIds, $node['id']);
        }

        DB::transaction(function () {
            foreach ($treeView as $treeNode) {
                DB::table('branches')->delete();
            }
        });
    }

    /**
     * Traversing tree using Depth-first search
     * Therefore the $treeView can display children nodes under each of the parent node.
     * 1. search children of parentId,
     * 2. then recursively search children of each children
     * 
     * @param $parentId the parentId used to search for children
     * @param $treeView the final list passed through the search
     * @param $prevLevel the previous level that parent node is on. The children will on the level = $prevLevel + 1
     */
    public function getChildrenBranches($parentId, &$treeView, $prevLevel)
    {
        $directcChildren = Branch::where('parent_id', $parentId)->get();
        $currentLevel = $prevLevel + 1;
        if ($directcChildren) {
            foreach ($directcChildren as $key => $child) {
                $treeNode = ['id' => $child->id, 'name' => $child->branch_name, 'level' => $currentLevel];
                array_push($treeView, $treeNode);
                $this->getChildrenBranches($child->id, $treeView, $currentLevel);
            }
        }

        return;
    }

    /**
     * Initial the children search status and result list.
     * @return $treeView,  the final list 
     */
    public function getAllChildrenBranches()
    {
        $treeView = [];
        $level = 0;
        $treeNode = ['id' => $this->id, 'name' => $this->branch_name, 'level' => $level];
        array_push($treeView, $treeNode);

        $this->getChildrenBranches($this->id, $treeView, $level);

        return $treeView;
    }
}
