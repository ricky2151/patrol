<?php

namespace App\Repositories\Contracts;

interface BaseRepositoryContract
{
    public function getAll();

    public function allOrder($orderBy, $orderType);

    public function find($id);

    public function getOneWhere($column, $value, $width);

    public function getManyWhere($column, $value);

    public function countWhere($column, $value);

    public function store(array $data);

    public function update(array $data, $id);

    public function delete($id);

    public function deleteWhere($column, $value);

    public function datatable();

    public function datatableWith(array $data);
}