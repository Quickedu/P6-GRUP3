<?php

namespace App\Http\Controllers\Workers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Worker\Admin\TestAdminRequest;
use App\Models\Test;
use Inertia\Inertia;

class TestAdminController extends Controller
{
    /**
     * List all tests for the admin view.
     */
    public function index()
    {
        $tests = Test::all();

        return Inertia::render('Workers/Admin/Test', [
            'tests' => $tests,
        ]);
    }

    /**
     * Show the form to create a new test.
     */
    public function create()
    {
        return Inertia::render('Workers/Admin/Create');
    }

    /**
     * Persist a new test from validated input.
     */
    public function store(TestAdminRequest $request)
    {
        $validated = $request->validated();
        Test::create($validated);

        return redirect()->back()->with(['status' => 'correcte', 'message' => 'Test creado correctamente']);

    }

    /**
     * Display a single test.
     */
    public function show(string $id)
    {
        return Inertia::render('Workers/Admin/Show', [
            'test' => Test::findOrFail($id),
        ]);
    }

    /**
     * Show the form to edit a test.
     */
    public function edit(string $id)
    {
        return Inertia::render('Workers/Admin/Edit', [
            'test' => Test::findOrFail($id),
        ]);
    }

    /**
     * Update an existing test from validated input.
     */
    public function update(TestAdminRequest $request, string $id)
    {
        $validated = $request->validated();
        $test = Test::findOrFail($id);
        $test->update($validated);

        return redirect()->back()->with(['status' => 'correcte', 'message' => 'Test actualizado correctamente']);
    }

    /**
     * Delete a test.
     */
    public function destroy(string $id)
    {
        $test = Test::findOrFail($id);
        $test->delete();

        return redirect()->back()->with(['status' => 'correcte', 'message' => 'Test eliminado correctamente']);
    }
}
