<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class File extends GenericModel
{
    protected $table = 'files';

    protected $fillable = [
		'file_name',
		'file_url',
		'status',
		'current_reserver_id',
		'publisher_id',
		'group_id',
		'is_accepted',
    ];

    /**
     * Get the group that owns the file
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'group_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'publisher_id', 'id');
    }

    public function reserver(){
        return $this->belongsTo(User::class, 'current_reserver_id', 'id');
    }

}
