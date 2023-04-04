<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Apartment;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ApartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $apartments = Apartment::where('user_id', Auth::id())->get();

        return view('admin.apartments.index', compact('apartments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $apartment = new Apartment();

        return view('admin.apartments.create', compact('apartment'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'price' => ['number', 'required'],
            'rooms' => ['number', 'required'],
            'beds' => ['number', 'required'],
            'square_meters' => ['number', 'nullable'],
            'bathrooms' => ['number', 'nullable'],
            'address' => ['string', 'required'],
            'thumb' => ['file'],
            'description' => ['string', 'nullable'],
        ]);

        $data = $request->all();

        $apartment = new Apartment();

        if (Arr::exists($data, 'thumb')) {
            $thumb_url = Storage::put('apartments', $data['thumb']);
            $data['thumb'] = $thumb_url;
        };

        $apartment->fill($data);

        $apartment->user_id = Auth::id();

        $apartment->save();

        return redirect()->route('admin.apartments.show', $apartment->id);
    }

    /**
     * Display the specified resource.
     */
    public function show(Apartment $apartment)
    {
        return view('admin.apartments.show', compact('apartment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $apartment = Apartment::findOrFail($id);
        return view('admin.apartments.edit', compact('apartment'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Apartment $apartment)
    {
        $data = $request->all();

        if (Arr::exists($data, 'thumb')) {
            if ($apartment->thumb) Storage::delete($apartment->thumb);
            $thumb_url = Storage::put('apartments', $data['thumb']);
            $data['thumb'] = $thumb_url;
        };

        $apartment->update($data);

        return redirect()->route('admin.apartments.show', $apartment->id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Apartment $apartment)
    {
        if ($apartment->thumb) Storage::delete($apartment->thumb);

        $apartment->delete();

        return to_route('admin.apartments.index');
    }
}
