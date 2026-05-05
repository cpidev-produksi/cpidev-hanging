<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShfiFolder extends Model
{
    use SoftDeletes;

    protected $table = 'shfi_folders';
    protected $fillable = ['root_id','parent_id','name','created_by'];

    public function root() { return $this->belongsTo(ShfiRoot::class, 'root_id'); }
    public function parent() { return $this->belongsTo(self::class, 'parent_id'); }
    public function children() { return $this->hasMany(self::class, 'parent_id'); }
}
