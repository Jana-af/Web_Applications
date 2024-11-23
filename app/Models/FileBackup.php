<?php

namespace App\Models;

class FileBackup extends GenericModel
{
    protected $table = 'file_backups';

    protected $fillable = [
        'file_id',
        'file_url',
        'version',
        'modifier_id',
        'version_date'
    ];


    public function modifier(){
        return $this->belongsTo(User::class, 'modifier_id', 'id');
    }
}
