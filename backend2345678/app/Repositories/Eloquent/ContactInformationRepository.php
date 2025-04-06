<?php

namespace App\Repositories\Eloquent;

use App\Models\ContactInformation;
use App\Repositories\Contracts\ContactInformationRepositoryInterface;

class ContactInformationRepository implements ContactInformationRepositoryInterface
{
    protected $model;

    public function __construct(ContactInformation $model)
    {
        $this->model = $model;
    }

    public function getAll()
    {
        return $this->model->when(!auth('api_users')->check(), function ($query) {
            $query->where('is_active', true);
        })->get();
    }

    public function getById($id)
    {
        return $this->model->when(!auth('api_users')->check(), function ($query) {
            $query->where('is_active', true);
        })->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $contactInfo = $this->getById($id);
        $contactInfo->update($data);
        return $contactInfo;
    }

    public function delete($id)
    {
        $contactInfo = $this->getById($id);
        $contactInfo->delete();
        return true;
    }
}