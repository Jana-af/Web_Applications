<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;


class Group extends GenericModel
{
    protected $table = 'groups';

	const CREATED_AT = null;

	const UPDATED_AT = null;

    protected $fillable = [
		'group_name',
		'group_type',
    ];
    
    /**
     * Get all files for the group
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function files(): HasMany
    {
        return $this->hasMany(File::class, 'group_id', 'id');
    }
    
    /**
     * Get all fileActionsLogs for the group
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function fileActionsLogs(): HasMany
    {
        return $this->hasMany(FileActionsLog::class, 'to_group', 'id');
    }
    
    /**
     * Get all groupUsers for the group
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function groupUsers(): HasMany
    {
        return $this->hasMany(GroupUser::class, 'group_id', 'id');
    }
}
