<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SocialLink\StoreSocialLinkRequest;
use App\Http\Requests\SocialLink\UpdateSocialLinkRequest;
use App\Http\Resources\SocialLinkResource;
use App\Repositories\Contracts\SocialLinkRepositoryInterface;


class SocialLinkController extends Controller
{
    protected $repository;
    public function __construct(SocialLinkRepositoryInterface $repository){
        $this->repository=$repository;
    }
    public function index(){
        $socialLink = $this->repository->getAll();
        return SocialLinkResource::collection($socialLink);
    }
    public function store(StoreSocialLinkRequest $request){
        $socialLink = $this->repository->create($request->validated());
        return new SocialLinkResource($socialLink);
    }
    public function show($id){
        $socialLink = $this->repository->getById($id);
        return new SocialLinkResource($socialLink);
    }
    public function update(UpdateSocialLinkRequest $request, $id)
    {
        $socialLink = $this->repository->update($id, $request->validated());
        return new SocialLinkResource($socialLink);
    }
    public function destroy($id){
        $this->repository->delete($id);
        return response()->json(['message'=> 'Social Link deleted successfully']);
    }
}
