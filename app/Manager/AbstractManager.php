<?php

namespace App\Manager;

use App\Enum\PaginationMetadataDefaultsEnum;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use App\DAL\DataAccessLayerInterface;
use Illuminate\Support\Collection;

/**
 * Superclass for all managers, exposing basic helper functionality for manipulating instances.
 *
 * @package Managers
 * @author  Dragos Becsan <dragos@coolfulsoft.com>
 */
abstract class AbstractManager
{
    /**
     * Get the data access layer that the manager interacts with.
     *
     * @return DataAccessLayerInterface
     */
    abstract public function getRepository();

    /**
     * Find all instances of the model.
     *
     * @param int    $itemsPerPage
     * @param string $orderBy
     * @param string $orderDirection
     * @param array  $loadRelationships
     *
     * @return Collection|LengthAwarePaginator
     */
    public function findAllPaginated(
        int $itemsPerPage = PaginationMetadataDefaultsEnum::ITEMS_PER_PAGE,
        string $orderBy = 'created_at',
        string $orderDirection = 'DESC',
        array $loadRelationships = []
    ) {
        return $this->getRepository()->findAllPaginated($itemsPerPage, $orderBy, $orderDirection, $loadRelationships);
    }

    /**
     * @param string $orderBy
     * @param string $orderDirection
     * @param array  $loadRelationships
     *
     * @return Collection
     */
    public function findAll(
        string $orderBy = 'created_at',
        string $orderDirection = 'DESC',
        array $loadRelationships = []
    ): Collection {
        return $this->getRepository()->findAll($orderBy, $orderDirection, $loadRelationships);
    }

    /**
     * Find all instances of the model by a given term.
     *
     * @param string $termKey
     * @param string $termValue
     * @param array  $otherCriteria
     * @param int    $itemsPerPage
     * @param string $orderBy
     * @param string $orderDirection
     * @param array  $loadRelationships
     *
     * @return mixed
     */
    public function findAllByTerm(
        string $termKey,
        string $termValue,
        array $otherCriteria = [],
        int $itemsPerPage = PaginationMetadataDefaultsEnum::ITEMS_PER_PAGE,
        string $orderBy = 'created_at',
        string $orderDirection = 'DESC',
        array $loadRelationships = []
    ) {
        return $this->getRepository()->findAllByTerm(
            $termKey,
            $termValue,
            $otherCriteria,
            $itemsPerPage,
            $orderBy,
            $orderDirection,
            $loadRelationships
        );
    }

    /**
     * @param string[] $ids
     *
     * @return Collection
     */
    public function findAllByIds(array $ids): Collection
    {
        return $this->getRepository()->findAllByIds($ids);
    }

    /**
     * Find a single instance of the model.
     *
     * @param string $id
     * @param array  $loadRelationships
     *
     * @return Model|null
     */
    public function findOne(string $id, array $loadRelationships = []): ?Model
    {
        return $this->getRepository()->find($id, $loadRelationships);
    }

    /**
     * Find a single instance of the model.
     *
     * @param string $id
     * @param array  $loadRelationships
     *
     * @return Model|null
     */
    public function findOneOrFail(string $id, array $loadRelationships = []): ?Model
    {
        return $this->getRepository()->findOrFail($id, $loadRelationships);
    }

    /**
     * Create a single instance of the model.
     *
     * @param array $data
     *
     * @return Model
     */
    public function create(array $data): Model
    {
        return $this->getRepository()->create($data);
    }

    /**
     * Persist a single instance of the model;
     *
     * @param Model $model
     *
     * @return Model
     */
    public function save(Model $model): Model
    {
        return $this->getRepository()->save($model);
    }

    /**
     * Find instances of the model that match the provided criteria.
     *
     * @param array    $criteria
     * @param string   $orderColumn
     * @param string   $orderDirection
     * @param string[] $loadRelationships
     *
     * @return Collection
     */
    public function findBy(
        array $criteria,
        string $orderColumn = 'created_at',
        string $orderDirection = 'DESC',
        array $loadRelationships = []
    ): Collection {
        return $this->getRepository()->findBy($criteria, $orderColumn, $orderDirection, $loadRelationships);
    }

    /**
     * Find instances of the model that match the provided criteria paginated.
     *
     * @param array  $criteria
     * @param int    $itemsPerPage
     * @param string $orderColumn
     * @param string $orderDirection
     * @param array  $loadRelationships
     *
     * @return LengthAwarePaginator
     */
    public function findByPaginated(
        array $criteria,
        int $itemsPerPage = PaginationMetadataDefaultsEnum::ITEMS_PER_PAGE,
        string $orderColumn = 'created_at',
        string $orderDirection = 'DESC',
        array $loadRelationships = []
    ): LengthAwarePaginator {
        return $this->getRepository()->findByPaginated(
            $criteria,
            $itemsPerPage,
            $orderColumn,
            $orderDirection,
            $loadRelationships
        );
    }

    /**
     * Find a single instance of the model that match the provided criteria.
     *
     * @param array    $criteria
     * @param string[] $loadRelationships
     *
     * @return Model|null
     */
    public function findOneBy(array $criteria, array $loadRelationships = []): ?Model
    {
        return $this->getRepository()->findOneBy($criteria, $loadRelationships);
    }

    /**
     * Update the $model instance with the provided data.
     *
     * @param Model $model
     * @param array $data
     *
     * @return Model
     */
    public function update(Model $model, array $data): Model
    {
        return $this->getRepository()->update($model, $data);
    }

    /**
     * Delete the $model instance.
     *
     * @param Model $model
     *
     * @return bool
     */
    public function delete(Model $model): bool
    {
        return $this->getRepository()->delete($model);
    }
}
