<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Product\FilterProductRequest;
use App\Http\Requests\Product\IndexProductRequest;
use App\Http\Requests\Product\SearchProductRequest;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\User;
use App\Repositories\Contracts\NotificationRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\ErrorCorrectionLevel;

class ProductController extends Controller
{
    protected $repository;

    public function __construct(ProductRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Lấy danh sách tất cả sản phẩm với phân trang và sắp xếp
     */
    public function index(IndexProductRequest $request)
    {
        $products = $this->repository->getAll(
            $request->get('sort_by', 'created_at'),
            $request->get('sort_direction', 'desc'),
            $request->get('per_page', 10)
        )->appends(request()->query());
        return ProductResource::collection($products);
    }

    /**
     * Tạo mới sản phẩm
     */
    public function store(StoreProductRequest $request)
    {
        $data = $request->validated();
        $product = $this->repository->create($data);
        $notificationRepo = app(NotificationRepositoryInterface::class);
        $superAdmins = User::where('is_super_admin', true)->get();
        foreach ($superAdmins as $superAdmin) {
            $notificationRepo->create([
                'user_type' => 'member',
                'user_id' => $superAdmin->id,
                'title' => "Sản phẩm mới được thêm: {$product->name}",
                'type' => 'new_product',                
            ]);
        }
        return new ProductResource($product);
    }

    /**
     * Lấy chi tiết sản phẩm theo ID
     */
    public function show($id)
    {
        $product = $this->repository->getById($id);
        return new ProductResource($product);
    }

    /**
     * Cập nhật sản phẩm
     */
    public function update(UpdateProductRequest $request, $id)
    {
        $data = $request->validated();
        $product = $this->repository->update($id, $data);
        $notificationRepo = app(NotificationRepositoryInterface::class);
        $superAdmins = User::where('is_super_admin', true)->get();
        foreach ($superAdmins as $superAdmin) {
            $notificationRepo->create([
                'user_type' => 'member',
                'user_id' => $superAdmin->id,
                'title' => "Sản phẩm có mã là {$product->product_code} vừa được cập nhật",
                'type' => 'updated_product',                
            ]);
        }
        return new ProductResource($product);
    }

    /**
     * Xóa mềm sản phẩm
     */
    public function destroy($id)
    {
        $this->repository->delete($id);
        return response()->json(['message' => 'Product deleted successfully'], 200);
    }

    /**
     * Lấy danh sách sản phẩm đã xóa mềm
     */
    public function trashed(IndexProductRequest $request)
    {
        $trashed = $this->repository->getTrashed(
            $request->get('sort_by', 'deleted_at'),
            $request->get('sort_direction', 'desc'),
            $request->get('per_page', 10)
        )->appends(request()->query());
        return ProductResource::collection($trashed);
    }

    /**
     * Khôi phục sản phẩm đã xóa mềm
     */
    public function restore($id)
    {
        $product = $this->repository->restore($id);
        return new ProductResource($product);
    }

    /**
     * Xóa vĩnh viễn sản phẩm
     */
    public function forceDelete($id)
    {
        $this->repository->forceDelete($id);
        return response()->json(['message' => 'Product permanently deleted'], 200);
    }

    /**
     * Lấy sản phẩm theo slug
     */
    public function getBySlug($slug)
    {
        $product = $this->repository->getBySlug($slug);
        return new ProductResource($product);
    }

    /**
     * Lấy sản phẩm theo product_code
     */
    public function getByProductCode($productCode)
    {
        $product = $this->repository->getByProductCode($productCode);
        return new ProductResource($product);
    }

    /**
     * Tìm kiếm sản phẩm theo tên (gần đúng) hoặc product_code (chính xác)
     */
    public function searchByName(SearchProductRequest $request)
    {
        $products = $this->repository->searchByName(
            $request->get('search'),
            $request->get('sort_by', 'created_at'),
            $request->get('sort_direction', 'desc'),
            $request->get('per_page', 10)
        )->appends(request()->query());
        return ProductResource::collection($products);
    }

    /**
     * Lọc sản phẩm với các bộ lọc
     */
    public function filter(FilterProductRequest $request)
    {
        $filters = $request->validated();
        $products = $this->repository->getFilteredProduct(
            $request->get('sort_by', 'created_at'),
            $request->get('sort_direction', 'desc'),
            $request->get('per_page', 10),
            $filters
        )->appends(request()->query());
        return ProductResource::collection($products);
    }

    /**
     * Tạo mã QR cho sản phẩm (không lưu file)
     */
    public function getQrCode($id)
    {
        $product = $this->repository->getById($id);
        $url = env('FRONTEND_URL') . "/products/{$product->slug}";

        // Tạo mã QR
        $qrCode = QrCode::create($url)
        ->setSize(300) 
        ->setMargin(10)
        ->setErrorCorrectionLevel(ErrorCorrectionLevel::High); // Tăng số chấm
        // Tạo logo
        $logo = Logo::create(public_path('logo.jpg')) // Đường dẫn đến logo
            ->setResizeToWidth(100) // Chiều rộng logo
            ->setResizeToHeight(100); // Chiều cao logo

        // Tạo writer và gắn logo
        $writer = new PngWriter();
        $result = $writer->write(
            $qrCode,
            $logo // Thêm logo vào QR
        );
        $base64String = base64_encode($result->getString());

        return response()->json([
            'data' => $base64String,
            'mime_type' => 'image/png'
        ], 200);
    }
}