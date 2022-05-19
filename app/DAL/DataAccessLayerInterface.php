<?php

namespace App\DAL;

use App\Enum\PaginationMetadataDefaultsEnum;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Defines a class that acts as a data access layer.
 *
 * @package App\DAL
 * @author  Dragos Becsan <dragos@coolfulsoft.com>
 */
interface DataAccessLayerInterface
{
    /**
     * Find all the instances of the current model.
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
    );

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
    ): Collection;

    /**
     * @param string[] $ids
     *
     * @return Collection
     */
    public function findAllByIds(array $ids): Collection;

    /**
     * Find an instance of the current model by id.
     *
     * @param string $id
     * @param array  $loadRelationships
     * @param array  $columns
     *
     * @return Model|null
     */
    public function find(string $id, array $loadRelationships = [], array $columns = ['*']): ?Model;

    /**
     * @param string $id
     * @param array  $loadRelationships
     * @param array  $columns
     *
     * @return Model
     */
    public function findOrFail(string $id, array $loadRelationships = [], array $columns = ['*']): Model;

    /**
     * Create an instance of the current model with the provided data.
     *
     * @param array $data
     *
     * @return Model
     */
    public function create(array $data): Model;

    /**
     * Persist an instance of the current model.
     *
     * @param Model $model
     *
     * @return Model
     */
    public function save(Model $model): Model;

    /**
     * Find instances of the current model that match the provided criteria.
     *
     * @param array  $criteria
     * @param string $orderColumn
     * @param string $orderDirection
     * @param array  $loadRelationships
     *
     * @return Collection
     */
    public function findBy(
        array $criteria,
        string $orderColumn,
        string $orderDirection,
        array $loadRelationships = []
    ): Collection;

    /**
     * Find instances of the current model that match the provided criteria.
     *
     * @param array  $criteria
     * @param int    $itemsPerPage
     * @param string $orderBy
     * @param string $orderDirection
     * @param array  $loadRelationships
     *
     * @return LengthAwarePaginator
     */
    public function findByPaginated(
        array $criteria,
        int $itemsPerPage = PaginationMetadataDefaultsEnum::ITEMS_PER_PAGE,
        string $orderBy = 'created_at',
        string $orderDirection = 'DESC',
        array $loadRelationships = []
    ): LengthAwarePaginator;

    /**
     * Find a single instance of the model that match the provided criteria.
     *
     * @param array    $criteria
     * @param string[] $loadRelationships
     *
     * @return Model|null
     */
    public function findOneBy(array $criteria, array $loadRelationships = []): ?Model;

    /**
     * Find all instances of the current model by the specified term.
     *
     * @param string $termKey
     * @param string $termValue
     * @param array  $otherCriteria
     * @param int    $itemsPerPage
     * @param string $orderBy
     * @param string $orderDirection
     * @param array  $loadRelationships
     *
     * @return Collection|LengthAwarePaginator
     */
    public function findAllByTerm(
        string $termKey,
        string $termValue,
        array $otherCriteria,
        int $itemsPerPage,
        string $orderBy,
        string $orderDirection,
        array $loadRelationships
    );

    /**
     * Update the provided instance with the provided data.
     *
     * @param Model $model
     * @param array $data
     *
     * @return Model
     */
    public function update(Model $model, array $data): Model;

    /**
     * Delete the provided instance.
     *
     * @param Model $model
     *
     * @return bool
     */
    public function delete(Model $model): bool;
}
