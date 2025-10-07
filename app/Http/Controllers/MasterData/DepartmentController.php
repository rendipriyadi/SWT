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
        return view('master-data.department.index', compact('departments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
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
