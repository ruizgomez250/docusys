<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'guard_name', 'created_at', 'updated_at',
    ];

    /**
     * The permissions that belong to the role.
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

    /**
     * Determine if the role has the given permission.
     *
     * @param string|Permission $permission
     * @return bool
     */
    public function hasPermissionTo($permission)
    {
        return $this->permissions()->where('name', $permission->name)->exists();
    }
}
