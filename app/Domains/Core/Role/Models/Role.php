<?php

namespace App\Domains\Core\Role\Models;

use App\Domains\Core\Permission\Models\Permission;
use App\Domains\Core\User\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Role extends Model
{
    use SoftDeletes;

    protected $table = 'roles';

    // Laravel will automatically handle timestamps if true (default is true)
    public $timestamps = true;

    // Tell Eloquent to treat these columns as Carbon instances
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    // Fillable columns - match your DB columns exactly
    protected $fillable = [
        'name',
    ];


    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_has_permission');
    }
}
