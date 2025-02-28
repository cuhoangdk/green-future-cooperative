<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Farm;
use App\Models\CustomerAddress;
use App\Models\Address;
use Illuminate\Support\Facades\DB;

class MigrateAddresses extends Command
{
    protected $signature = 'migrate:addresses';
    protected $description = 'Di chuyển dữ liệu địa chỉ từ bảng cũ sang bảng addresses';

    public function handle()
    {
        DB::transaction(function () {
            // Di chuyển địa chỉ của User
            User::whereNotNull('province')->each(function ($user) {
                Address::updateOrCreate(
                    [
                        'addressable_id' => $user->id, 
                        'addressable_type' => User::class
                    ],
                    [
                        'province' => $user->province,
                        'district' => $user->district,
                        'ward' => $user->ward,
                        'street_address' => $user->street_address,
                    ]
                );
            });

            // Di chuyển địa chỉ của Farm
            Farm::whereNotNull('province')->each(function ($farm) {
                Address::updateOrCreate(
                    [
                        'addressable_id' => $farm->id, 
                        'addressable_type' => Farm::class
                    ],
                    [
                        'province' => $farm->province,
                        'district' => $farm->district,
                        'ward' => $farm->ward,
                        'street_address' => $farm->street_address,
                    ]
                );
            });

            // Di chuyển địa chỉ của CustomerAddress
            CustomerAddress::whereNotNull('province')->each(function ($customerAddress) {
                Address::updateOrCreate(
                    [
                        'addressable_id' => $customerAddress->id, 
                        'addressable_type' => CustomerAddress::class
                    ],
                    [
                        'province' => $customerAddress->province,
                        'district' => $customerAddress->district,
                        'ward' => $customerAddress->ward,
                        'street_address' => $customerAddress->street_address,
                    ]
                );
            });

            $this->info('Đã di chuyển xong dữ liệu địa chỉ!');
        });
    }

}
