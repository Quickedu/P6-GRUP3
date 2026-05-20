<?php

namespace App\Http\Controllers\Workers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Worker\Admin\WorkerAdminRequest;
use App\Models\User;
use App\Models\Worker;
use Inertia\Inertia;

class WorkerAdminController extends Controller
{
    /**
     * List all workers with related user details for the admin view.
     */
    public function index()
    {
        $workers = Worker::query()
            ->with('user:id,name,email,role')
            ->get()
            ->map(function ($worker) {
                return [
                    'id' => $worker->id,
                    'name' => $worker->user->name,
                    'email' => $worker->user->email,
                    'role' => $worker->user->role,
                    'nss' => $worker->nss,
                    'address' => $worker->address,
                    'dni' => $worker->dni,
                    'license_number' => $worker->license_number,
                    'phone' => $worker->phone,
                ];
            });

        return Inertia::render('Workers/Admin/Worker', [
            'workers' => $workers,
        ]);
    }

    /**
     * Reserved for a create form (not implemented here).
     */
    public function create()
    {
    }

    /**
     * Create a user and the linked worker record.
     */
    public function store(WorkerAdminRequest $request)
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'password' => $validated['password'],
        ]);

        Worker::create([
            'user_id' => $user->id,
            'nss' => $validated['nss'],
            'address' => $validated['address'],
            'dni' => $validated['dni'],
            'license_number' => $validated['license_number'],
            'phone' => $validated['phone'],
        ]);

        return redirect()->back()->with(['status' => 'correcte', 'message' => 'Treballador creat correctament']);
    }

    /**
     * Display a single worker.
     */
    public function show(string $id)
    {
        return Inertia::render('Admin/Workers/Show', [
            'worker' => Worker::findOrFail($id),
        ]);
    }

    /**
     * Show the form to edit a worker.
     */
    public function edit(string $id)
    {
        return Inertia::render('Admin/Workers/Edit', [
            'worker' => Worker::findOrFail($id),
        ]);
    }

    /**
     * Update the worker and its related user record.
     */
    public function update(WorkerAdminRequest $request, Worker $worker)
    {
        $validated = $request->validated();

        $worker = Worker::query()->findOrFail($worker->id);
        $worker->user()->update([
            'email' => $validated['email'],
            'role' => $validated['role'],
        ]);
        $worker->update($validated);

        return redirect()->back()->with(['status' => 'correcte', 'message' => 'Treballador actualitzat correctament']);
    }

    /**
     * Delete the worker and its related user record.
     */
    public function destroy(string $id)
    {
        $worker = Worker::findOrFail($id);
        $worker->user()->delete();
        $worker->delete();

        return redirect()->back()->with(['status' => 'correcte', 'message' => 'Treballador eliminat correctament']);
    }
}
