<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DealRequest;
use App\Http\Resources\DealResource;
use App\Repositories\DealRepositoryInterface;

class DealController extends Controller
{
    protected DealRepositoryInterface $dealRepository;

    public function __construct(DealRepositoryInterface $dealRepository)
    {
        $this->dealRepository = $dealRepository;
    }

    public function index()
    {
        return DealResource::collection($this->dealRepository->all());
    }

    public function store(DealRequest $request)
    {
        $deal = $this->dealRepository->create($request->validated());
        return new DealResource($deal);
    }

    public function show($id)
    {
        $deal = $this->dealRepository->find($id);
        return new DealResource($deal);
    }

    public function update(DealRequest $request, $id)
    {
        $deal = $this->dealRepository->find($id);
        $deal = $this->dealRepository->update($deal, $request->validated());
        return new DealResource($deal);
    }

    public function destroy($id)
    {
        $deal = $this->dealRepository->find($id);
        $this->dealRepository->delete($deal);
        return response()->json(['message' => 'Сделка удалена']);
    }
}
