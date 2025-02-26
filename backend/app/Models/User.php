<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\GeneratesUserCode;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;


class User extends Authenticatable
{
    use HasFactory, HasApiTokens, Notifiable, GeneratesUserCode, SoftDeletes;

    protected $table = 'users'; // Đổi tên bảng từ cooperative_members thành users

    /**
     * Các cột có thể được gán giá trị hàng loạt.
     *
     * @var array
     */
    protected $fillable = [
        'email', 
        'password', 
        'full_name', 
        'bank_account_number', 
        'bank_name',
        'date_of_birth',
        'phone_number', 
        'avatar_url', 
        'bio',         
        'is_super_admin', 
        'is_banned',        
        'province', 
        'district', 
        'ward', 
        'street_address',
        'usercode', 
        'last_login_at',
        'gender',
        'role_id',
    ];

    /**
     * Các cột ẩn không trả về trong JSON.
     *
     * @var array
     */
    protected $hidden = ['password'];

    /**
     * Các cột sẽ được cast sang kiểu dữ liệu mong muốn.
     *
     * @var array
     */
    protected $casts = [
        'date_of_birth' => 'date',
        'is_super_admin' => 'boolean',
        'is_banned' => 'boolean',
        'last_login_at' => 'datetime',
        'province' => 'string',
        'district' => 'string',
        'ward' => 'string',
    ];    

    /**
     * Quan hệ với bài viết (posts).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function posts()
    {
        return $this->hasMany(Post::class, 'user_id');
    }
    /**
     * Quan hệ với quyền (roles)
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Role, User>
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
    /**
     * Quan hệ với chức năng (permission)
     * @param mixed $permission
     * @return bool
     */
    public function hasPermission($permission)
    {
        if ($this->is_super_admin) {
            return true;
        }

        return $this->role && $this->role->permissions->contains('name', $permission);
    }
}
