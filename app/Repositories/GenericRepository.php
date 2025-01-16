<?php

namespace App\Repositories;

use Exception;

class GenericRepository
{
    protected $model;

    public function __construct($model)
    {
        $this->model = $model;
    }

    public function getAll()
    {
        return $this->model::paginate(request()['limit']);
    }

    public function getCount()
    {
        return $this->model::count();
    }

    public function findById($id)
    {
        $model = $this->model::find($id);

        if (!$model) {
            throw new Exception(__('validation.exists', ['attribute' => __('models.' . class_basename($this->model))]), 404);
        }

        return $model;
    }


    public function create($data)
    {
        return $this->model::create($data);
    }


    public function bulkCreate(array $data)
    {
        return collect($data)->map(fn($item) => $this->create($item));
    }


    public function update($model, $data)
    {
        $model->update($data);
        return $model;
    }


    public function delete($model)
    {
        $model->delete();
    }

    public function bulkDelete(array $ids)
    {
        $this->model::whereIn('id', $ids)->delete();
    }
}
