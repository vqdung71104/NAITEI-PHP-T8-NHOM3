<?php

namespace App\Contracts\Repositories;

interface CategoryRepository extends BaseRepository
{
    public function all();
    public function paginate($items = null);
    public function find($id);
}

