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


    public function files(): HasMany
    {
        return $this->hasMany(File::class, 'group_id', 'id');
    }

    public function fileActionsLogs(): HasMany
    {
        return $this->hasMany(FileActionsLog::class, 'to_group', 'id');
    }

    public function groupUsers(): HasMany
    {
        return $this->hasMany(GroupUser::class, 'group_id', 'id');
    }

    public function users(){
        return $this->belongsToMany(User::class)->withPivot([
            'is_owner',
            'is_accepted'
        ]);;
    }
}
