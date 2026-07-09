<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreHouseRequest;
use App\Models\House;
use Illuminate\Http\Request;

class HouseController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(House::class, 'house');
    }

    public function index(Request $request)
    {
        $houses = House::query()
            ->when($request->search, fn ($q) => $q->where('owner_name', 'like', "%{$request->search}%")
                ->orWhere('house_number', 'like', "%{$request->search}%"))
            ->orderBy('block')
            ->orderBy('house_number')
            ->paginate(15)
            ->withQueryString();

        return view('admin.houses.index', compact('houses'));
    }

    public function create()
    {
        return view('admin.houses.create');
    }

    public function store(StoreHouseRequest $request)
    {
        House::create($request->validated());

        return redirect()->route('admin.houses.index')->with('success', 'Data rumah berhasil ditambahkan.');
    }

    public function edit(House $house)
    {
        return view('admin.houses.edit', compact('house'));
    }

    public function update(StoreHouseRequest $request, House $house)
    {
        $house->update($request->validated());

        return redirect()->route('admin.houses.index')->with('success', 'Data rumah berhasil diperbarui.');
    }

    public function destroy(House $house)
    {
        $house->update(['is_active' => false]);

        return back()->with('success', 'Rumah dinonaktifkan.');
    }
}
