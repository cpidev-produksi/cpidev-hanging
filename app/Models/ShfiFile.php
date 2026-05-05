<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShfiFile extends Model
{
    use SoftDeletes;

    protected $table = 'shfi_files';
    protected $fillable = [
        'root_id','folder_id','name','disk','disk_path','mime_type','size','uploaded_by','uploaded_at'
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
    ];

    public function root() { return $this->belongsTo(ShfiRoot::class, 'root_id'); }
    public function folder() { return $this->belongsTo(ShfiFolder::class, 'folder_id'); }
}
