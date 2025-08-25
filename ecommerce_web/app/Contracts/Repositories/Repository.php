<?php

namespace App\Contracts\Repositories;

interface BaseRepository
{   
    public function index();

    public function store($data = []);

    public function update($id, $data = []);

    public function destroy($id);

    public function show($id);
}

