<?php

namespace App\Repositories;

use App\Models\Client;

class ClientRepository implements ClientRepositoryInterface
{
    public function all($perPage = 10)
    {
        return Client::latest()->paginate($perPage);
    }

    public function create(array $data)
    {
        return Client::create($data);
    }

    public function find($id)
    {
        return Client::findOrFail($id);
    }

    public function update($client, array $data)
    {
        $client->update($data);
        return $client;
    }

    public function delete($client)
    {
        return $client->delete();
    }
}