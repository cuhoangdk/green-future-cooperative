<?php

namespace App\Repositories\Eloquent;

use App\Models\SocialLink;
use App\Repositories\Contracts\SocialLinkRepositoryInterface;

class SocialLinkRepository implements SocialLinkRepositoryInterface{
    protected $model;
    public function __construct(SocialLink $model){
        $this->model=$model;
    }
    public function getAll(){
        return $this->model->when(!auth('api_users')->check(), function ($query) {
            $query->where('is_active', true);
        })->get();
    }
    public function getById($id){
        return $this->model->when(!auth('api_users')->check(), function ($query) {
            $query->where('is_active', true);
        })->findOrFail($id);
    }
    public function create(array $data){
        return $this->model->create($data);
    }
    public function update($id, array $data){
        $socialLink = $this->getById($id);
        $socialLink->update($data);
        return $socialLink;
    }
    public function delete($id){
        $socialLink = $this->getById($id);
        $socialLink->delete();
        return true;
    }
}

