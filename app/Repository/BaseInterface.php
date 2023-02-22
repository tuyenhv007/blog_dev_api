<?php

namespace App\Repository;

interface BaseInterface
{
    public function getAll();

    public function find($id);

    public function create($attributes = []);

    public function update($id, $attributes = []);

    public function delete($id);

    public function toggleActive($id);

    public function find_foreignKey($id, $table, $collection);

    public function delete_field($field, $value);

    public function count_find_foreignKey($id, $table, $collection);

    public function findOne($condition);

    public function findMany($condition);

    public function findOneDesc($condition);

    public function findManySortColumn($condition, $colum, $sort);

    public function findOneSortColumn($condition, $colum, $sort);

    public function find_one($key, $value);

    public function count($condition);
}
