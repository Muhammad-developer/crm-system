<?php

namespace App\Repositories;
interface DealRepositoryInterface
{
    public function all($perPage = 10);

    public function create(array $data);

    public function find($id);

    public function update($deal, array $data);

    public function delete($deal);
}
