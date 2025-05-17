<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClientRequest;
use App\Http\Resources\ClientResource;
use App\Repositories\ClientRepositoryInterface;

class ClientController extends Controller
{
    protected $clientRepository;

    public function __construct(ClientRepositoryInterface $clientRepository)
    {
        $this->clientRepository = $clientRepository;
    }

    public function index()
    {
        return ClientResource::collection($this->clientRepository->all());
    }

    public function store(ClientRequest $request)
    {
        $client = $this->clientRepository->create($request->validated());
        return new ClientResource($client);
    }

    public function show($id)
    {
        $client = $this->clientRepository->find($id);
        return new ClientResource($client);
    }

    public function update(ClientRequest $request, $id)
    {
        $client = $this->clientRepository->find($id);
        $client = $this->clientRepository->update($client, $request->validated());
        return new ClientResource($client);
    }

    public function destroy($id)
    {
        $client = $this->clientRepository->find($id);
        $this->clientRepository->delete($client);
        return response()->json(['message' => 'Клиент удалён']);
    }
}