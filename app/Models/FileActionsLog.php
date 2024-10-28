<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;


class FileActionsLog extends GenericModel
{
    protected $table = 'file_actions_log';

	const UPDATED_AT = null;

    protected $fillable = [
		'file_id',
		'user_id',
		'action',
		'to_group',
		'old_file_name',
		'new_file_name',
	];

    /**
     * Get the group that owns the fileActionsLog
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'to_group', 'id');
    }
}

