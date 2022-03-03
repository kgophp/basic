<?php

namespace YK\Basic\Controllers;

use Illuminate\Http\Request;
use YK\Basic\Models\AdminParam;
use YK\Basic\Resources\DataCollection;

class AdminParamController extends Controller
{
    use HasResourceActions;
    protected $modelName='YK\Basic\Models\AdminParam';

    /**
     * @author  nash<  >
     * @param Request $request
     * @return DataCollection
     */
    public function index(Request $request)
    {
        $params =AdminParam::select(['id','param_name','param_value'])
            ->where(request_like_sect(['param_name']))
            ->paginate($this->getPageSize($request));

        return new DataCollection($params);
    }

   /*
    * @author  nash<  >
    * 保存数据之前，设置model的 $attributes
    * @param Request $request,$model,$attributes
    * @return array
    */
    public function beforeSaving(Request $request,$model,&$attributes)
    {
        $isNew=empty($model)||count($model)==0;
        $id=0;
        if (!$isNew)
            $id=$model->id;

        $this->validate($request, [
            'param_name' => 'required|unique:admin_params,param_name,'.$id.',id|max:197',
            'param_value' => 'required|max:197',
        ]);

        $attributes = request_intersect([
            'param_name', 'param_value'
        ]);

    }

    /**
     * 获得model名
     * @author nash<  >
     * @param
     * @return String
     */
    public function getModelName(){
        return $this->modelName;
    }

    /**
     * 删除前的数据检查
     * @author  nash<  >
     * @param $model
     */
    private function beforeDeleting($model){

    }


}
