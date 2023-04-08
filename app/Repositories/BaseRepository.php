<?php

namespace App\Repositories;

use App\Repositories\Contracts\RepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class BaseRepository implements RepositoryInterface
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * BaseRepository constructor.
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function all()
    {
        return $this->model->all();
    }

    public function find($id)
    {
        return $this->model->findOrFail($id);
    }

    public function create($request)
    {
        return $this->model->create($request->all());
    }

    public function update($id, $request)
    {
        $record = $this->model->findOrFail($id);
        $record->update($request->all());
        return $record;
    }

    public function delete($id)
    {
        $record = $this->model->findOrFail($id);
        return $record->delete();
    }
}