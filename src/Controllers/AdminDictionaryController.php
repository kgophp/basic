<?php

namespace YK\Basic\Controllers;

use Illuminate\Http\Request;
use YK\Basic\Models\AdminDictionary;
use YK\Basic\Resources\DataCollection;

class AdminDictionaryController extends Controller
{
    use HasResourceActions;
    protected $modelName='YK\Basic\Models\AdminDictionary';
    /**
     * @author  nash<  >
     * @param Request $request
     * @return DataCollection
     */
    public function index(Request $request)
    {
        $data =AdminDictionary::select(['id','parent_id','dict_type','dict_key','dict_value','sort'])
            ->orderBy('parent_id','asc')
            ->orderBy('sort','asc')
            ->get();

        return response()->json(['data' => make_tree($data->toArray())]);
    }
    /**
     * @author  nash<  >
     * 保存数据之前，设置model的 $attributes
     * @param Request $request
     * @return array
     */
    public function beforeSaving(Request $request,$model,&$attributes)
    {
        $isNew=empty($model)||count($model)==0;
        $id=0;
        if ($isNew){
            //新建
           $attributes = request_intersect([
              'dict_key', 'dict_value','dict_type','sort','tenant_id'
           ],false);
           if (empty($attributes['dict_type']))
               throw new \Exception('Dictinary Type is null');
           if ($attributes['dict_type']=='Root')
               $attributes['parent_id']=0;
           else{
               $model= AdminDictionary::where('dict_key',$attributes['dict_type'])
                   ->where('parent_id',0)
                   ->get();
               if (count($model)==0)
                   throw new \Exception('Dictinary Type is  not exists!');
               $attributes['parent_id']=$model[0]->id;
           }
        }
        else{
        //更新
           $id=$model->id;
           $attributes = request_intersect([
              'dict_key', 'dict_value','sort',
           ],false);
            $attributes['parent_id']= AdminDictionary::where('id',$model->id)->first()['parent_id'];

        }
        $this->validate($request, [
            'dict_key' => 'required|unique:admin_dictionaries,dict_key,'.$id.',id,parent_id,'.$attributes['parent_id'].'|max:197',
            'dict_value' => 'required|max:197',
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
     * @author  nash<  >
     * @param $id
     * @return json
     */
    public function getTypeList(Request $request)
    {
        $dict_type=$request->get('dict_type');

        if (empty($dict_type))
            throw new \Exception('Dictinary Type is null');
        $builder=AdminDictionary::select('id','dict_key as value','dict_value as label')
            ->where(request_intersect(['dict_type','dict_key']))
            ->orderBy('parent_id')
            ->orderBy('sort');
         $data=$builder->get()->toArray();

        if ($dict_type=="Root")
            array_unshift($data,['id'=>0,'value'=>'Root','label'=>'根节点']);
        return response()->json(['data' =>$data]);
    }

    /**
     * 删除前的数据检查
     * @author  nash<  >
     * @param $model
     */
    private function beforeDeleting($model){
        $count=AdminDictionary::where('parent_id',$model->id)->count();
        if ($count>0)
            throw  new \Exception('该类型下存在数据，不允许删除！');

    }

}
