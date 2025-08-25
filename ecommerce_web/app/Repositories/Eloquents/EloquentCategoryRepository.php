<?php

namespace App\Repositories\Eloquents;

use App\Models\User;
use App\Contracts\Repositories\CategoryRepository;

class EloquentCategoryRepository extends EloquentBaseRepository implements CategoryRepository
{
    protected $model;

    public function __construct(Category $model)
    {
        $this->model = $model;
    }

    public function all()
    {
        return $this->model->with('name')->get();
    }

    public function find($id)
    {
        return $this->model->find($id);
    }
}

