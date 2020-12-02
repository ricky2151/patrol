<?php

namespace App\Repositories\Contracts;

interface BaseRepositoryContract
{
    public function getAll();

    public function allOrder($orderBy, $orderType);

    public function get($id);

    public function getOneWhere($column, $value, $width);

    public function getManyWhere($column, $value);

    public function countWhere($column, $value);

    public function create(array $data);

    public function update(array $data, $id);

    public function delete($id);

    public function deleteWhere($column, $value);

    public function datatable($select);

    public function datatableWith($select, array $data);
}