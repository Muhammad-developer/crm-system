<?php

namespace App\Repositories;

interface ClientRepositoryInterface
{
    public function all($perPage = 10);
    public function create(array $data);
    public function find($id);
    public function update($client, array $data);
    public function delete($client);
}