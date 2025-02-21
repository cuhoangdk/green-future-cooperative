<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PostComments\StoreCommentRequest;
use App\Http\Requests\PostComments\UpdateCommentRequest;
use App\Http\Resources\PostCommentResource;
use App\Repositories\Contracts\PostCommentRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class PostCommentController extends Controller
{
    protected $commentRepository;

    public function __construct(PostCommentRepositoryInterface $commentRepository)
    {
        $this->commentRepository = $commentRepository;
    }
    /**
     * Lấy danh sách comment của 1 bài viết
     * 
     * @param mixed $postId - postId cần hiện comment
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection - trả về danh sách comment
     */
    public function index($postId)
    {
        $comments = $this->commentRepository->getAllComments($postId);
        return PostCommentResource::collection($comments);
    }
    /**
     * Thêm comment của 1 bài viết
     * 
     * @param mixed $postId - postId cần đăng comment
     * @param \App\Http\Requests\PostComments\StoreCommentRequest $request - chứa `content`
     * @return PostCommentResource - trả về nồi dung comment
     */
    public function store($postId, StoreCommentRequest $request)
    {
        $data = $request->validated();
        $data['post_id'] = $postId;
        $data['customer_id'] = Auth::guard('api_customers')->id();

        $comment = $this->commentRepository->create($data);
        return new PostCommentResource($comment);
    }
    /**
     * Sửa comment
     * @param \App\Http\Requests\PostComments\UpdateCommentRequest $request - chứa `content`
     * @param mixed $postId - postId cần sửa comment
     * @param mixed $id - id của comment
     * @return mixed|PostCommentResource|\Illuminate\Http\JsonResponse - trả về nội dung comment hoặc thông báo JSON
     */
    public function update(UpdateCommentRequest $request,$postId, $id)
    {
        $data = $request->validated();
        $comment = $this->commentRepository->getByPostAndId($postId, $id);
        if (!$comment) {
            return response()->json(['message' => 'Comment not found'], 404);
        }
        if (Auth::guard('api_customers')->id() !== $comment->customer_id && !Auth::guard('api_users')->check()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $updatedComment = $this->commentRepository->update($id, $data);
        return new PostCommentResource($updatedComment);
    }
    /**
     * Xoá comment
     * @param mixed $postId - postId cần xoá comment
     * @param mixed $id - id của comment
     * @return mixed|\Illuminate\Http\JsonResponse  - trả về thông báo JSON
     */
    public function destroy($postId, $id)
    {
        $comment = $this->commentRepository->getByPostAndId($postId, $id);
        if (Auth::guard('api_customers')->id() !== $comment->customer_id && !Auth::guard('api_users')->check()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if (!$comment) {
            return response()->json(['message' => 'Comment not found'], 404);
        }
        

        $this->commentRepository->delete($id);
        return response()->json(['message' => 'Comment deleted successfully']);
    }
}
