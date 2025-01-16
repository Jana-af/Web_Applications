<?php

namespace App\Services;

use App\Annotations\Transactional;
use App\Repositories\GenericRepository;
use Exception;
use App\Models\GenericModel;

class GenericService
{
    private GenericModel $model;
    protected GenericRepository $repository;

    public function __construct(GenericModel $model, GenericRepository $repository)
    {
        $this->model = $model;
        $this->repository = $repository;
    }

    /**
     * Retrieve all records from the model.
     *
     * @return Collection A collection of all records in the model.
     */
    public function getAll()
    {
        return $this->repository->getAll();
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
         return $this->repository->findById($modelId);
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
        return $this->repository->create($validatedData);
    }

    /**
     * Store multiple records in the model.
     *
     * @param array $validatedData The data to be validated and stored.
     */
    #[Transactional]
    public function bulkStore($validatedData)
    {
        return $this->repository->bulkCreate($validatedData['list']);
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
        return $this->repository->update($model, $validatedData);
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
        $this->repository->delete($model);
    }

    /**
     * Delete multiple records in the model.
     *
     * @param array $validatedData The validated data containing the list of IDs to delete.
     */

    #[Transactional]
    public function bulkDelete($validatedData)
    {
        $this->repository->bulkDelete($validatedData['ids']);
    }
}
