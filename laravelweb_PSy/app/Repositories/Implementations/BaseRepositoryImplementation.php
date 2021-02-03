<?php

namespace App\Repositories\Implementations;

use App\Repositories\Contracts\BaseRepositoryContract;

use DB;

class BaseRepositoryImplementation implements BaseRepositoryContract
{
    protected $model;

    public function getModel()
    {
        return $this->model;
    }

    public function setModel($model)
    {
        $this->model = $model;
    }

    public function getAll()
    {
        return $this->model->get();
    }

    public function allOrder($orderBy, $orderType)
    {
        return $this->orderBy($orderBy, $orderType)->get();
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function getOneWhere($column, $value, $with)
    {
        return $this->model->with($with)->where($column, $value)->first();
    }

    public function getManyWhere($column, $value)
    {
        $tempStr = implode(',', $value);
        return $this->model->whereIn($column, (array) $value)->orderByRaw(DB::raw("FIELD($column, $tempstr)"))->get();
    }

    public function countWhere($column, $value)
    {
        return $this->model->where($column = '', $value)->count();
    }

    public function store(array $data)
    {
        $newData = $this->model->create($data);   
        return $newData->toArray();
    }

    public function update(array $data, $id)
    {
        return $this->model->whereIn('id', (array) $id)->update($data);
    }

    public function delete($id)
    {
        return $this->model->find($id)->delete();
    }

    public function deleteWhere($column, $value)
    {
        return $this->model->where($column, $value)->delete();
    }

    public function datatable($select)
    {
        return $this->model->select();
    }

    public function datatableWIth($select, array $with)
    {
        return $this->datatable($select)->with($with);
    }
}
