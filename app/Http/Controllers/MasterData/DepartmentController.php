<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\DepartemenSupervisor as Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $departments = Department::orderBy('created_at', 'desc')->get();
        
        // \SharedManager::saveLog('log_sitime', "Accessed the [Department/Supervisor] page swt.");
        
        return view('master-data.department.index', compact('departments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // \SharedManager::saveLog('log_sitime', "Accessed the [Create Department/Supervisor] page swt.");
        
        return view('master-data.department.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'supervisor' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'workgroup' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Department::create([
            'supervisor' => $request->supervisor,
            'departemen' => $request->name,
            'workgroup' => $request->workgroup,
            'email' => $request->email,
        ]);

        // \SharedManager::saveLog('log_sitime', "Created new department/supervisor: {$request->name} swt.");
        
        return redirect()->route('master-data.department.index')
            ->with('success', 'Supervisor created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $department = Department::findOrFail($id);
        return view('master-data.department.show', compact('department'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $department = Department::findOrFail($id);
        
        // \SharedManager::saveLog('log_sitime', "Accessed the [Edit Department/Supervisor] page for ID: {$id} swt.");
        
        return view('master-data.department.edit', compact('department'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Department $department)
    {
        $validator = Validator::make($request->all(), [
            'supervisor' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'workgroup' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $department->update([
            'supervisor' => $request->supervisor,
            'departemen' => $request->name,
            'workgroup' => $request->workgroup,
            'email' => $request->email,
        ]);

        // \SharedManager::saveLog('log_sitime', "Updated department/supervisor ID: {$department->id} swt.");
        
        return redirect()->route('master-data.department.index')
            ->with('success', 'Supervisor updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $department = Department::findOrFail($id);
        $department->delete();

        // \SharedManager::saveLog('log_sitime', "Deleted department/supervisor ID: {$id} swt.");
        
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
    public function forceDelete($id)
    {
        $department = Department::withTrashed()->findOrFail($id);
        $department->forceDelete();

        return redirect()->route('master-data.department.index')
            ->with('success', 'Supervisor permanently deleted.');
    }
}
