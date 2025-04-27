<?php

namespace App\Repositories\Eloquent;

use App\Models\ContactMessage;
use App\Repositories\Contracts\ContactMessageRepositoryInterface;
use Illuminate\Support\Facades\DB;

class ContactMessageRepository implements ContactMessageRepositoryInterface
{
    protected $model;

    public function __construct(ContactMessage $model)
    {
        $this->model = $model;
    }

    public function getAll(
        string $sortBy = 'created_at',
        string $sortDirection = 'desc',
        int $perPage = null
    ) {
        $query = $this->model->orderBy($sortBy, $sortDirection);

        return $perPage ? $query->paginate($perPage) : $query->get();
    }


    public function getById($id)
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            return $this->model->create($data);
        });
    }

    public function update($id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $message = $this->getById($id);
            $message->update($data);
            return $message;
        });
    }

    public function delete($id)
    {
        $message = $this->getById($id);
        $message->delete();
        return $message;
    }
}
