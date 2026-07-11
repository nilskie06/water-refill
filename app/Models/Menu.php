<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'url', 'icon', 'parent_id', 'position', 'is_active', 'target'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'position' => 'integer',
        ];
    }

    public function parent()
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id')->orderBy('position');
    }

    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id')->orderBy('position');
    }

    public static function buildTree($items = null)
    {
        if ($items === null) {
            $items = static::where('is_active', true)->orderBy('position')->get();
        }

        $grouped = $items->groupBy('parent_id');
        $tree = [];

        foreach ($grouped[null] ?? [] as $item) {
            $item->setRelation('children', $grouped[$item->id] ?? collect());
            $tree[] = $item;
        }

        return $tree;
    }
}
