<?php

namespace App\Repositories\Eloquent;

use App\Models\SliderImage;
use App\Repositories\Contracts\SliderImageRepositoryInterface;

class SliderImageRepository implements SliderImageRepositoryInterface
{
    protected $model;

    public function __construct(SliderImage $model)
    {
        $this->model = $model;
    }

    public function getAll()
    {
        return $this->model->when(!auth('api_users')->check(), function ($query) {
            $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('start_date')
                  ->orWhere('start_date', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', now());
            });;
        })->get();
    }

    public function getById($id)
    {
        return $this->model->when(!auth('api_users')->check(), function ($query) {
            $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('start_date')
                  ->orWhere('start_date', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', now());
            });;
        })->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $sliderImage = $this->getById($id);
        $sliderImage->update($data);
        return $sliderImage;
    }

    public function delete($id)
    {
        $sliderImage = $this->getById($id);
        $sliderImage->delete();
        return true;
    }
}