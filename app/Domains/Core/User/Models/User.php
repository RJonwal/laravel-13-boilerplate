<?php

namespace App\Domains\Core\User\Models;

use App\Domains\Api\Auth\Emails\SendResetPasswordOtpMail;
use App\Domains\Core\Role\Models\Role;
use App\Domains\Core\Permission\Models\Permission;
use App\Domains\Core\Upload\Models\Uploads;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'uuid',
        'name',
        'email',
        'password',
        'phone',
        'fcm_token',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected static function boot ()
    {
        parent::boot();
        static::creating(function(User $model) {
            $model->uuid = Str::uuid();
        });
    }

    public function sendPasswordResetOtpNotification($user, $token, $subject, $expiretime)
    {
        Mail::to($user->email)->send(new SendResetPasswordOtpMail($user, $token, $subject, $expiretime));
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function hasRole($roleId)
    {
        return $this->roles()->where('id', $roleId)->exists();
    }

    public function getIsSuperAdminAttribute()
    {
        return $this->roles()->where('id', config('constant.roles.super_admin'))->exists();
    }

    public function getIsStaffAttribute()
    {
        return $this->roles()->where('id', config('constant.roles.staff'))->exists();
    }

    public function getIsAccountantAttribute()
    {
        return $this->roles()->where('id', config('constant.roles.accountant'))->exists();
    }
    
    public function uploads()
    {
        return $this->morphMany(Uploads::class, 'uploadsable');
    }

    public function profileImage()
    {
        return $this->morphOne(Uploads::class, 'uploadsable')->where('type', 'user_profile');
    }

    public function getProfileImageUrlAttribute()
    {
        if ($this->profileImage) {
            return $this->profileImage->file_url;
        }
        return "";
    }

    public function getAllPermissionsAttribute()
    {
        return $this->roles()->with('permissions')->get()
            ->pluck('permissions')
            ->flatten()  // Single collection
            ->unique('id');
    }

}
