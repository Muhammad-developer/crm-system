<?php
namespace App\Repositories;

use App\Models\Deal;

class DealRepository implements DealRepositoryInterface
{
    public function all($perPage = 10)
    {
        return Deal::with('client', 'user')->latest()->paginate($perPage);
    }

    public function create(array $data)
    {
        return Deal::create($data);
    }

    public function find($id)
    {
        return Deal::with('client', 'user')->findOrFail($id);
    }

    public function update($deal, array $data)
    {
        $deal->update($data);
        return $deal;
    }

    public function delete($deal)
    {
        return $deal->delete();
    }
}
