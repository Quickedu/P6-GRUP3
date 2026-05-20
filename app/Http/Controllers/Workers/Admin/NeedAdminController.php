<?php

namespace App\Http\Controllers\Workers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Worker\Admin\NeedAdminRequest;
use App\Models\Need;
use Inertia\Inertia;

class NeedAdminController extends Controller
{
    /**
     * List all needs for the admin view.
     */
    public function index()
    {
        $needs = Need::all();

        return Inertia::render('Workers/Admin/Need', [
            'needs' => $needs,
        ]);
    }

    /**
     * Show the form to create a new need.
     */
    public function create()
    {
        return Inertia::render('Workers/Admin/CreateNeed');
    }

    /**
     * Persist a new need from validated input.
     */
    public function store(NeedAdminRequest $request)
    {
        $validated = $request->validated();
        Need::create($validated);

        return redirect()->back()->with(['status' => 'correcte', 'message' => 'Necessitat creada correctamente']);
    }

    /**
     * Display a single need.
     */
    public function show(string $id)
    {
        return Inertia::render('Workers/Admin/ShowNeed', [
            'need' => Need::findOrFail($id),
        ]);
    }

    /**
     * Show the form to edit a need.
     */
    public function edit(string $id)
    {
        return Inertia::render('Workers/Admin/EditNeed', [
            'need' => Need::findOrFail($id),
        ]);
    }

    /**
     * Update an existing need from validated input.
     */
    public function update(NeedAdminRequest $request, string $id)
    {
        $validated = $request->validated();
        $need = Need::findOrFail($id);
        $need->update($validated);

        return redirect()->back()->with(['status' => 'correcte', 'message' => 'Necessitat actualitzada correctamente']);
    }

    /**
     * Delete a need.
     */
    public function destroy(string $id)
    {
        $need = Need::findOrFail($id);
        $need->delete();

        return redirect()->back()->with(['status' => 'correcte', 'message' => 'Necessitat eliminada correctamente']);
    }
}
