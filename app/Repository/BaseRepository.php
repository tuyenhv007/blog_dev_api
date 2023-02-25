<?php

namespace App\Repository;

use Illuminate\Support\Facades\DB;

abstract class BaseRepository implements BaseInterface
{
    const DESC_SORT = 'DESC';
    const ASC_SORT = 'ASC';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $model;

    public function __construct()
    {
        $this->setModel();
    }

    abstract public function getModel();

    public function setModel()
    {
        $this->model = app()->make(
            $this->getModel()
        );
    }

    public function getAll()
    {
        return $this->model->all();
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function create($attributes = [])
    {
        $this->model->create($attributes);
    }

    public function update($id, $attributes = [])
    {
        $record = $this->find($id);
        if ($record) {
            $record->update($attributes);
            return $record;
        }
        return false;
    }

    public function delete($id)
    {
        $record = $this->find($id);
        if ($record) {
            $record->delete();
            return true;
        }
        return false;
    }

    public function queryByConditions($condition)
    {
        $query = $this->model;
        foreach ($condition as $nameColumn => $valueColumn) {
            if (is_array($valueColumn)) {
                $operator = $valueColumn[0];
                $searchValue = $valueColumn[1];
                $query = $query->where($nameColumn, $operator, $searchValue);
            } else {
                $query = $query->where($nameColumn, $valueColumn);
            }
        }
        return $query;
    }

    public function toggleActive($id)
    {
        $record = $this->find($id);
        if ($record) {
            if ($record->status == 'active') {
                $update_status = 'deactivated';
            } else {
                $update_status = 'active';
            }
            if ($update_status) {
                $this->update($id, [
                    'status' => $update_status
                ]);
            }
        }
        return null;
    }

    public function find_foreignKey($id, $relationship, $collection)
    {
        $record = $this->model->whereHas($relationship, function ($query) use ($id, $collection) {
            $query->where($collection, $id);
        })->get();

        return $record;
    }

    public function delete_field($field, $value)
    {
        DB::beginTransaction();
        try {
            $this->model->where($field, $value)->delete();
            DB::commit();
            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            return false;
        }
    }

    public function count_find_foreignKey($id, $relationship, $collection)
    {
        $record = $this->model->whereHas($relationship, function ($query) use ($id, $collection) {
            $query->where($collection, $id);
        })->count();

        return $record;
    }

    public function findOne($condition)
    {
        $query = $this->queryByConditions($condition);
        return $query->first();
    }

    public function findOneSelect($condition, $select = [])
    {
        $query = $this->queryByConditions($condition);
        return $query->select($select)->first();
    }

    public function findAll($condition)
    {
        $query = $this->queryByConditions($condition);
        return $query->get();
    }

    public function findAllSelect($condition, $select = [])
    {
        $query = $this->queryByConditions($condition);
        return $query->select($select)->get();
    }

    public function findOneDesc($condition)
    {

    }

    public function findManySortColumn($condition, $colum, $sort)
    {
        // TODO: Implement findManySortColumn() method.
    }

    public function findOneSortColumn($condition, $colum, $sort)
    {
        // TODO: Implement findOneSortColumn() method.
    }

    public function find_one($key, $value)
    {
        $query = $this->model;
        $query = $query->where($key, $value);
        return $query->first();
    }

    public function find_one_select($key, $value, $select)
    {
        $query = $this->model;
        $query = $query->where($key, $value);
        return $query->first()->select($select);
    }

    public function count($condition)
    {
        $query = $this->model;
        foreach ($condition as $key => $value) {
            $query = $query->where($key, $value);
        }
        return $query
            ->count();
    }
}
