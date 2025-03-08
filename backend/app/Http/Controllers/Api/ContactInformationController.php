<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContactInformation\StoreContactInformationRequest;
use App\Http\Requests\ContactInformation\UpdateContactInformationRequest;
use App\Http\Resources\ContactInformationResource;
use App\Repositories\Contracts\ContactInformationRepositoryInterface;

class ContactInformationController extends Controller
{
    protected $repository;

    public function __construct(ContactInformationRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function index()
    {
        $contactInfos = $this->repository->getAll();
        return ContactInformationResource::collection($contactInfos);
    }

    public function store(StoreContactInformationRequest $request)
    {
        $contactInfo = $this->repository->create($request->validated());
        return new ContactInformationResource($contactInfo);
    }

    public function show($id)
    {
        $contactInfo = $this->repository->getById($id);
        return new ContactInformationResource($contactInfo);
    }

    public function update(UpdateContactInformationRequest $request, $id)
    {
        $contactInfo = $this->repository->update($id, $request->validated());
        return new ContactInformationResource($contactInfo);
    }

    public function destroy($id)
    {
        $this->repository->delete($id);
        return response()->json(null, 204);
    }
}