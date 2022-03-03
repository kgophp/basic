<?php
/**
 * Created by PhpStorm.
 * User: nash
 * Date: 2019-05-20
 * Time: 14:17
 */

namespace YK\Basic\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait HasResourceActions
{
    /**
     * 新建
     * @author  nash<  >
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //验证数据
        $attributes=[];
        $this->handSaving($request,null,$attributes);
        $attributes['created_by']=Auth::user()->id;
        $attributes['updated_by']=Auth::user()->id;
        app($this->getModelName())::create($attributes);

        return $this->created();
    }


    /**
     * 修改
     * @author  nash<  >
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = app($this->getModelName())::query()->findOrFail($id);
        $attributes=[];
        $this->handSaving($request,$data,$attributes);
        $attributes['updated_by']=Auth::user()->id;

        $data->update($attributes);
        return $this->noContent();
    }

    /**
     * 数据保存前事件
     * @author  nash<  >
     * @param $model
     */
    protected function handSaving(Request $request,$model,&$attributes){
        if(method_exists($this,'beforeSaving')){
            $this->beforeSaving($request,$model,$attributes);
        }
    }

    /**
     * 数据删除前的事件
     * @author  nash<  >
     * @param $model
     */
    protected function handDeleting($model){
        if(method_exists($this,'beforeDeleting')){
            $this->beforeDeleting($model);
        }
    }


    /**
     * 删除
     * @author  nash<  >
     * @param $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = app($this->getModelName())::query()->findOrFail($id);
        $this->handDeleting($data);
        if (method_exists($data, 'isForceDeleting') && !$data->isForceDeleting()){
            if (property_exists($data,'deleted') && empty($data->deleted))
                $data->deleted=1;
            $data->updated_by=Auth::user()->id;
            $data->save();
        }
        $data->delete();
        return $this->noContent();
    }

    /**
     * 批量删除
     * @author  nash<  >
     * @param $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function batchDestroy(Request $request)
    {
        $data=$request->get('data');
        $modelType=app($this->getModelName());
        DB::beginTransaction();
        try {
            foreach($data as $row){
                $model=$modelType::findOrFail($row['id']);
                $this->handDeleting($model);
                $model->delete();
            }
            DB::commit();
        }catch (\Exception $e){
            DB::rollback();
            throw $e;
        }
        return $this->noContent();
    }


}
