<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\DepartemenSupervisor as Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    /**
     */
    public function index()
    {
        $departments = Department::orderBy('created_at', 'desc')->get();

        // \SharedManager::saveLog('log_swt', "Accessed the [Department/Supervisor] page swt.");

        return view('master-data.department.index', compact('departments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // \SharedManager::saveLog('log_swt', "Accessed the [Create Department/Supervisor] page swt.");

        return view('master-data.department.create');
    }

    public function store(Request $request)
    {
        Department::create([
            'departemen' => $request->name,
            'supervisor' => $request->supervisor ?? $request->name,
            'workgroup' => $request->workgroup,
        ]);

        // \SharedManager::saveLog('log_swt', "Created new department/supervisor: {$request->name} swt.");

        return redirect()->route('master-data.department.index')
            ->with('success', 'Supervisor created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Department $department)
    {
        return view('master-data.department.show', compact('department'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Department $department)
    {
        // \SharedManager::saveLog('log_swt', "Accessed the [Edit Department/Supervisor] page for ID: {$department->id} swt.");

        return view('master-data.department.edit', compact('department'));
    }

    public function update(Request $request, Department $department)
    {
        $department->update([
            'departemen' => $request->name,
            'supervisor' => $request->supervisor ?? $request->name,
            'workgroup' => $request->workgroup,
        ]);

        // \SharedManager::saveLog('log_swt', "Updated department/supervisor ID: {$department->id} swt.");

        return redirect()->route('master-data.department.index')
            ->with('success', 'Supervisor updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Department $department)
    {
        $department->delete();

        // \SharedManager::saveLog('log_swt', "Deleted department/supervisor ID: {$department->id} swt.");

        return redirect()->route('master-data.department.index')
            ->with('success', 'Supervisor deleted successfully.');
    }

    /**
     * Restore the specified resource from storage.
     */
    // Soft deletes not used on departemen_supervisors

    /**
     * Permanently delete the specified resource from storage.
     */
    public function forceDelete(Department $department)
    {
        $department->forceDelete();

        return redirect()->route('master-data.department.index')
            ->with('success', 'Supervisor permanently deleted.');
    }
}
