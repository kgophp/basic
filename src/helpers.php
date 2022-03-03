<?php

if (!function_exists('request_intersect')) {
    /**
     * request intersect
     *
     * @param $keys
     * @param $rmZero 是否清除值为0的key
     * @return array|ø
     */
    function request_intersect($keys,$rmZero=true) {

        if ($rmZero)
            return array_filter(request()->only(is_array($keys) ? $keys : func_get_args()));
        else
            return array_filter(request()->only(is_array($keys) ? $keys : func_get_args()),function ($key){
            if($key === '' || $key === null){
                return false;
            }
            return true;
        });
    }
}

if (!function_exists('request_like_sect')) {
    /**
     * request intersect
     *
     * @param $keys
     * @return array|ø
     */
    function request_like_sect($keys) {

        $data = array_filter(request()->only(is_array($keys) ? $keys : func_get_args()));

        $likeWhere = [];
        foreach ($data as $key=>$val){
            $likeWhere[] = [$key , 'like' , '%'.$val.'%'];
        }

        return $likeWhere;
    }
}

if (!function_exists('admin_path')) {

    /**
     * Get admin path.
     *
     * @param string $path
     *
     * @return string
     */
    function admin_path($path = '')
    {
        return ucfirst(config('admin.directory')).($path ? DIRECTORY_SEPARATOR.$path : $path);
    }
}

if (!function_exists('make_tree')) {
    /**
     * @param array $list
     * @param int $parentId
     * @return array
     */
    function make_tree(array $list, $parentId = 0) {
        $tree = [];
        if (empty($list)) {
            return $tree;
        }

        $newList = [];
        foreach ($list as $k => $v) {
            $newList[$v['id']] = $v;
        }

        foreach ($newList as $value) {
            if ($parentId == $value['parent_id']) {
                $tree[] = &$newList[$value['id']];
            } elseif (isset($newList[$value['parent_id']])) {
                $newList[$value['parent_id']]['children'][] = &$newList[$value['id']];
            }
        }

        return $tree;
    }
}
