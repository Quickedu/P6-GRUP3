<?php

namespace App\Http\Controllers\Workers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Requests\Worker\Admin\TestAdminRequest;
use App\Models\Test;

class TestAdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tests = Test::all();
        return Inertia::render('Workers/Admin/Test', [
            'tests' => $tests,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Workers/Admin/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TestAdminRequest $request)
    {
        $validated = $request->validated();
        Test::create($validated);
        return redirect()->back()->with(['status' => 'correcte', 'message' => 'Test creado correctamente']);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Inertia::render('Workers/Admin/Show', [
            'test' => Test::findOrFail($id),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return Inertia::render('Workers/Admin/Edit', [
            'test' => Test::findOrFail($id),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TestAdminRequest $request, string $id)
    {
        $validated = $request->validated();
        $test = Test::findOrFail($id);
        $test->update($validated);
        return redirect()->back()->with(['status' => 'correcte', 'message' => 'Test actualizado correctamente']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $test = Test::findOrFail($id);
        $test->delete();
        return redirect()->back()->with(['status' => 'correcte', 'message' => 'Test eliminado correctamente']);
    }
}
