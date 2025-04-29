<?php

namespace App\Services;

use App\Http\Controllers\Api\ActivityLogController;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ActivityLogService
{
    protected $request;

    // Danh sách các trường nhạy cảm cần ẩn
    protected $sensitiveFields = [
        'password',
        'remember_token',
        'access_token',
        'refresh_token',
    ];

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function log($action, $entityType, $oldData = null, $newData = null)
    {
        // Lọc dữ liệu nhạy cảm
        $filteredOldData = $this->filterSensitiveData($oldData);
        $filteredNewData = $this->filterSensitiveData($newData);

        $logData = [
            'action' => $action,
            'entity_type' => $entityType,
            'old_data' => $filteredOldData ? json_encode($filteredOldData) : null,
            'new_data' => $filteredNewData ? json_encode($filteredNewData) : null,
            'ip_address' => $this->request->ip(),
            'user_agent' => $this->request->userAgent(),
        ];

        if (auth('api_users')->check()) {
            $logData['user_type'] = 'member';
            $logData['user_id'] = auth('api_users')->id();
        } elseif (auth('api_customers')->check()) {
            $logData['user_type'] = 'customer';
            $logData['user_id'] = auth('api_customers')->id();
        } else {
            $logData['user_type'] = 'system';
            $logData['user_id'] = null;
        }

        app(ActivityLogController::class)->store($logData);
    }

    public function logFromRequest($action, $entityType)
    {
        $oldData = $this->getOldData($entityType);
        $newData = $this->request->method() === 'DELETE' ? null : $this->request->all();

        $this->log($action, $entityType, $oldData, $newData);
    }

    protected function getOldData($entityType)
    {
        if (in_array($this->request->method(), ['PUT', 'PATCH', 'DELETE'])) {
            $id = $this->request->route('id') ?? $this->request->route('product_id') ?? $this->request->route('customerId');
            if ($id) {
                $modelClass = $this->mapEntityTypeToModel($entityType);
                return $modelClass::find($id)?->toArray();
            }
        }
        return null;
    }

    protected function mapEntityTypeToModel($entityType)
    {
        \Log::info("Entity Type: " . $entityType); // Ghi log để kiểm tra
        $map = [
            'social-links' => 'App\\Models\\SocialLink',
            'slider-images' => 'App\\Models\\SliderImage',
            'contact-informations' => 'App\\Models\\ContactInformation',
            'orders' => 'App\\Models\\Order',
            'admin' => 'App\\Models\\Order',
            'cart' => 'App\\Models\\CartItem',
            'products' => 'App\\Models\\Product',
            'product-categories' => 'App\\Models\\ProductCategory',
            'product-units' => 'App\\Models\\ProductUnit',
            'farms' => 'App\\Models\\Farm',
            'roles' => 'App\\Models\\Role',
            'customers' => 'App\\Models\\Customer',
            'posts' => 'App\\Models\\Post',
            'post-categories' => 'App\\Models\\PostCategory',
            'cultivation-logs' => 'App\\Models\\CultivationLog',
            'quantity-prices' => 'App\\Models\\ProductQuantityPrice',
            'images' => 'App\\Models\\ProductImage',
            'addresses' => 'App\\Models\\CustomerAddress',
            'comments' => 'App\\Models\\PostComment',
            'users' => 'App\\Models\\User',
            'shipping-fee' => 'App\\Models\\Parameter',
            'customer-profile' => 'App\\Models\\Customer',
        ];

        return $map[$entityType] ?? 'App\\Models\\' . ucfirst(Str::camel($entityType));
    }

    protected function filterSensitiveData($data)
    {
        if (!$data || !is_array($data)) {
            return $data;
        }

        foreach ($this->sensitiveFields as $field) {
            if (isset($data[$field])) {
                unset($data[$field]);
            }
        }

        return $data;
    }
}