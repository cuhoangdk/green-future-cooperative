<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContactMessage\IndexContactMessageRequest;
use App\Http\Requests\ContactMessage\StoreContactMessageRequest;
use App\Http\Requests\ContactMessage\UpdateContactMessageRequest;
use App\Repositories\Contracts\ContactMessageRepositoryInterface;
use App\Http\Resources\ContactMessageResource;


class ContactMessageController extends Controller
{
    protected $repo;

    public function __construct(ContactMessageRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function index(IndexContactMessageRequest $request)
    {
        $sortBy = $request->input('sort_by', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');
        $perPage = $request->input('per_page', 10);

        $messages = $this->repo->getAll($sortBy, $sortDirection, $perPage);

        return ContactMessageResource::collection($messages);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreContactMessageRequest $request)
    {
        $data = $request->validated();

        $message = $this->repo->create($data);

        return new ContactMessageResource($message);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $message = $this->repo->getById($id);

        return new ContactMessageResource($message);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id, UpdateContactMessageRequest $request)
    {
        $data = $request->validated();

        $message = $this->repo->update($id, $data);

        return new ContactMessageResource($message);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $message = $this->repo->delete($id);

        return response()->json([
            'message' => 'Contact message deleted successfully',
            'data' => new ContactMessageResource($message),
        ]);
    }
}
