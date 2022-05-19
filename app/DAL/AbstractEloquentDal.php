<?php

namespace App\DAL;

use App\Enum\PaginationMetadataDefaultsEnum;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Application;
use Illuminate\Support\Collection;

/**
 * Superclass for all Eloquent repositories, exposing common interactions with the database.
 *
 * @package App\DAL
 * @author  Dragos Becsan <dragos@coolfulsoft.com>
 */
abstract class AbstractEloquentDal implements DataAccessLayerInterface
{
    /**
     * @var Builder
     */
    protected $model;

    /**
     * AbstractEloquentRepository constructor.
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->model = $app->make($this->getModel());
    }

    /**
     * Get the current model.
     *
     * @return string
     */
    abstract public function getModel(): string;

    /**
     * @inheritdoc
     */
    public function findAllPaginated(
        int $itemsPerPage = PaginationMetadataDefaultsEnum::ITEMS_PER_PAGE,
        string $orderBy = 'created_at',
        string $orderDirection = 'DESC',
        array $loadRelationships = []
    ) {
        $query = $this->model->with($loadRelationships)->orderBy($orderBy, $orderDirection);

        return $itemsPerPage ? $query->paginate($itemsPerPage) : $query->get();
    }

    public function findAll(
        string $orderBy = 'created_at',
        string $orderDirection = 'DESC',
        array $loadRelationships = []
    ): Collection {
        return $this->model->with($loadRelationships)->newQuery()->orderBy($orderBy, $orderDirection)->get();
    }

    public function findAllByIds(array $ids): Collection
    {
        return $this->model->newQuery()->whereIn('id', $ids)->get();
    }

    /**
     * @inheritdoc
     */
    public function find(string $id, array $loadRelationships = [], array $columns = ['*']): ?Model
    {
        return $this->model->with($loadRelationships)->newQuery()->find($id, $columns);
    }

    /**
     * @inheritdoc
     */
    public function findOrFail(string $id, array $loadRelationships = [], array $columns = ['*']): Model
    {
        return $this->model->with($loadRelationships)->newQuery()->findOrFail($id, $columns);
    }

    /**
     * @inheritdoc
     */
    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    /**
     * @inheritdoc
     */
    public function save(Model $model): Model
    {
        $model->save();

        return $model;
    }

    /**
     * @inheritdoc
     */
    public function findBy(
        array $criteria,
        string $orderColumn = 'created_at',
        string $orderDirection = 'DESC',
        array $loadRelationships = []
    ): Collection {
        $model = $this->model->with($loadRelationships)->newQuery();
        foreach ($criteria as $column => $value) {
            $model->where($column, $value);
        }

        return $model->orderBy($orderColumn, $orderDirection)->get();
    }

    /**
     * @inheritdoc
     */
    public function findByPaginated(
        array $criteria,
        int $itemsPerPage = PaginationMetadataDefaultsEnum::ITEMS_PER_PAGE,
        string $orderBy = 'created_at',
        string $orderDirection = 'DESC',
        array $loadRelationships = []
    ): LengthAwarePaginator {
        $model = $this->model->with($loadRelationships)->newQuery();
        foreach ($criteria as $column => $value) {
            if (is_array($value)) {
                $model->where($column, $value['operator'], $value['value']);
                continue;
            }
            $model->where($column, $value);
        }

        return $model->orderBy($orderBy, $orderDirection)->paginate($itemsPerPage);
    }

    /**
     * @inheritdoc
     */
    public function findOneBy(array $criteria, array $loadRelationships = []): ?Model
    {
        $model = $this->model->newQuery();
        foreach ($criteria as $column => $value) {
            $model->where($column, $value);
        }

        return $model->first();
    }

    /**
     * @inheritDoc
     */
    public function findAllByTerm(
        string $termKey,
        string $termValue,
        array $otherCriteria = [],
        int $itemsPerPage = PaginationMetadataDefaultsEnum::ITEMS_PER_PAGE,
        string $orderBy = 'created_at',
        string $orderDirection = 'desc',
        array $loadRelationships = []
    ) {
        $model = $this->model->with($loadRelationships)
                             ->newQuery();
        foreach ($otherCriteria as $column => $value) {
            $model->where($column, $value);
        }

        return $model->where($termKey, 'like', sprintf('%%%s%%', $termValue))
                     ->orderBy($orderBy, $orderDirection)
                     ->paginate($itemsPerPage);
    }

    /**
     * @inheritdoc
     */
    public function update(Model $model, array $data): Model
    {
        $model->update($data);

        return $model;
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function delete(Model $model): bool
    {
        return $model->delete();
    }


}
