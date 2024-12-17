<?php

namespace App\Services;

use App\Annotations\Transactional;
use Exception;
use App\Models\GenericModel;

class GenericService
{
    private GenericModel $model;


    public function __construct(GenericModel $model)
    {
        $this->model = $model;
    }

    /**
     * Retrieve all records from the model.
     *
     * @return Collection A collection of all records in the model.
     */
    public function getAll()
    {
        return $this->model::paginate(request()['limit']);
    }

    /**
     * Retrieve a record by its ID from the model.
     *
     * @param int $modelId The ID of the record to retrieve.
     * @return Model The retrieved model instance.
     * @throws Exception if the record is not found (HTTP 404 error).
     */
    public function findById($modelId)
    {
        $model = $this->model::find($modelId);

        if (!$model) {
            throw new Exception(__('validation.exists', ['attribute' => __('models.' . class_basename($this->model))]), 404);
        }

        return $model;
    }

    /**
     * Store a new record in the model.
     *
     * @param array $validatedData The data to be validated and stored.
     * @return Model The newly created model instance.
     */
    #[Transactional]
    public function store($validatedData)
    {
        $model = $this->model::create($validatedData);

        return $model;
    }

    /**
     * Store multiple records in the model.
     *
     * @param array $validatedData The data to be validated and stored.
     * @return Collection A collection of newly created model instances.
     */
    #[Transactional]
    public function bulkStore($validatedData)
    {
        $items = collect($validatedData['list'])->map(function ($record) {
            return $this->model::create($record);
        });

        return $items;
    }

    /**
     * Update an existing record in the model.
     *
     * @param array $validatedData The data to be validated and updated.
     * @param int $modelId The ID of the record to update.
     * @return Model The updated model instance.
     */
    #[Transactional]
    public function update($validatedData, $modelId)
    {
        $model = $this->findById($modelId);

        $model->update($validatedData);

        return $model;
    }

    /**
     * Delete a record from the model.
     *
     * @param int $modelId The ID of the record to delete.
     */
    #[Transactional]
    public function delete($modelId)
    {
        $model = $this->findById($modelId);

        $model->delete();
    }

    /**
     * Delete multiple records in the model.
     *
     * @param array $validatedData The validated data containing the list of IDs to delete.
     */
    #[Transactional]
    public function bulkDelete($validatedData)
    {
        $this->model::whereIn('id', $validatedData['ids'])->delete();
    }
}
