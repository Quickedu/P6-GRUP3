<?php

namespace App\Http\Controllers\Workers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Worker\Admin\NeedAdminRequest;
use App\Models\Need;
use Inertia\Inertia;

class NeedAdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $needs = Need::all();

        return Inertia::render('Workers/Admin/Need', [
            'needs' => $needs,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Workers/Admin/CreateNeed');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(NeedAdminRequest $request)
    {
        $validated = $request->validated();
        Need::create($validated);

        return redirect()->back()->with(['status' => 'correcte', 'message' => 'Necessitat creada correctamente']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Inertia::render('Workers/Admin/ShowNeed', [
            'need' => Need::findOrFail($id),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return Inertia::render('Workers/Admin/EditNeed', [
            'need' => Need::findOrFail($id),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(NeedAdminRequest $request, string $id)
    {
        $validated = $request->validated();
        $need = Need::findOrFail($id);
        $need->update($validated);

        return redirect()->back()->with(['status' => 'correcte', 'message' => 'Necessitat actualitzada correctamente']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $need = Need::findOrFail($id);
        $need->delete();

        return redirect()->back()->with(['status' => 'correcte', 'message' => 'Necessitat eliminada correctamente']);
    }
}
