<?php

if (!function_exists('listToTree')) {
    function listToTree(array &$list, $parentId = 0)
    {
        $tree = [];
        foreach ($list as &$item) {
            if ($item['pid'] == $parentId) {
                $children = listToTree($list, $item['id']);
                if ($children) {
                    $item['children'] = $children;
                }
                $tree[] = $item;
                unset($item);
            }
        }
        return $tree;
    }
}