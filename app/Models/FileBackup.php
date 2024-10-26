<?php

namespace App\Models;

class FileBackup extends GenericModel
{
    protected $table = 'file_backups';

    protected $fillable = [
		'file_id',
		'file_url',
    ];
}
