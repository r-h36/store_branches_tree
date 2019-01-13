<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Branch extends Model
{
    //


    /**
     * function to delete all the children assign to this branch
     * Use DB transaction to make sure the deleting is done properly, otherwise roll back the changes.
     * 
     * @return boolean indicates whether the deleting is done properly or not.
     */
    public function deleteTree()
    {
        $tree = $this->getAllChildrenBranches();

        $branchIds = [];
        foreach ($tree as $node) {
            array_push($branchIds, $node['id']);
        }

        $branchIds = array_map(function ($item) {
            return $item['id'];
        }, $tree);

        DB::beginTransaction();
        try {
            DB::table('branches')->whereIn('id', $branchIds)->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();

            return false;
        }

        return true;
    }

    /**
     * Traversing tree using Depth-first search
     * Therefore the $treeList can display children nodes under each of the parent node.
     * 1. search children of parentId,
     * 2. then recursively search children of each children
     * 
     * @param $parentId the parentId used to search for children
     * @param $treeList the final list passed through the search
     * @param $prevLevel the previous level that parent node is on. The children will on the level = $prevLevel + 1
     */
    public function getChildrenBranches($parentId, &$treeList, $prevLevel)
    {
        $directcChildren = Branch::where('parent_id', $parentId)->get();
        $currentLevel = $prevLevel + 1;
        if ($directcChildren) {
            foreach ($directcChildren as $key => $child) {
                $treeNode = ['id' => $child->id, 'name' => $child->branch_name, 'level' => $currentLevel];
                array_push($treeList, $treeNode);
                $this->getChildrenBranches($child->id, $treeList, $currentLevel);
            }
        }

        return;
    }

    /**
     * Initial the children search status and result list.
     * @return $treeList,  the final tree list which list all the nodes in the tree, with the level of the node.
     */
    public function getAllChildrenBranches()
    {
        $treeList = [];
        $level = 0;
        $treeNode = ['id' => $this->id, 'name' => $this->branch_name, 'level' => $level];
        array_push($treeList, $treeNode);

        $this->getChildrenBranches($this->id, $treeList, $level);

        return $treeList;
    }

    public function isAncestorOf(Branch $child)
    {
        return $this->compareAncestor($child, $this->id);
    }

    public function compareAncestor(Branch $child, int $idToCompare)
    {
        if ($child->parent_id === 0) {
            return false;
        }
        if ($child->parent_id === $idToCompare) {
            return true;
        } else {
            $nextChildToCompare = Branch::where('id', $child->parent_id)->first();
            return $this->compareAncestor($nextChildToCompare, $idToCompare);
        }
    }
}
