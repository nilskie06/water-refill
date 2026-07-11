<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::with('children')->topLevel()->get();
        return response()->json($menus);
    }

    public function tree()
    {
        $tree = Menu::buildTree();
        return response()->json($tree);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:menus,id',
            'is_active' => 'boolean',
            'target' => 'in:_self,_blank',
        ]);

        $data['position'] = Menu::where('parent_id', $data['parent_id'] ?? null)->max('position') + 1;
        $data['is_active'] = $data['is_active'] ?? true;

        $menu = Menu::create($data);
        return response()->json($menu, 201);
    }

    public function show(Menu $menu)
    {
        return response()->json($menu->load('children'));
    }

    public function update(Request $request, Menu $menu)
    {
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'url' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:menus,id',
            'is_active' => 'boolean',
            'target' => 'in:_self,_blank',
        ]);

        // Prevent self-referencing
        if (isset($data['parent_id']) && $data['parent_id'] == $menu->id) {
            $data['parent_id'] = null;
        }

        $menu->update($data);
        return response()->json($menu->fresh()->load('children'));
    }

    public function destroy(Menu $menu)
    {
        // Move children to parent
        Menu::where('parent_id', $menu->id)->update(['parent_id' => $menu->parent_id]);
        $menu->delete();
        return response()->json(['message' => 'Deleted']);
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:menus,id',
            'items.*.position' => 'required|integer|min:0',
            'items.*.parent_id' => 'nullable|exists:menus,id',
        ]);

        foreach ($request->items as $item) {
            Menu::where('id', $item['id'])->update([
                'position' => $item['position'],
                'parent_id' => $item['parent_id'] ?? null,
            ]);
        }

        return response()->json(['message' => 'Reordered']);
    }
}
