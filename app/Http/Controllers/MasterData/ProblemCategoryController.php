<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\ProblemCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProblemCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = ProblemCategory::ordered()->get();
        
        \SharedManager::saveLog('log_sitime', "Accessed the [Problem Category] page swt.");
        
        return view('master-data.problem-category.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        \SharedManager::saveLog('log_sitime', "Accessed the [Create Problem Category] page swt.");
        
        return view('master-data.problem-category.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:problem_categories,name',
            'description' => 'nullable|string',
            'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        ProblemCategory::create([
            'name' => $request->name,
            'description' => $request->description,
            'color' => $request->color,
            'is_active' => true,
            'sort_order' => ProblemCategory::max('sort_order') + 1
        ]);

        \SharedManager::saveLog('log_sitime', "Created new problem category: {$request->name} swt.");
        
        return redirect()->route('master-data.problem-category.index')
            ->with('success', 'Problem category created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ProblemCategory $problemCategory)
    {
        return view('master-data.problem-category.show', compact('problemCategory'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProblemCategory $problemCategory)
    {
        \SharedManager::saveLog('log_sitime', "Accessed the [Edit Problem Category] page for ID: {$problemCategory->id} swt.");
        
        return view('master-data.problem-category.edit', compact('problemCategory'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProblemCategory $problemCategory)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:problem_categories,name,' . $problemCategory->id,
            'description' => 'nullable|string',
            'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $problemCategory->update([
            'name' => $request->name,
            'description' => $request->description,
            'color' => $request->color
        ]);

        \SharedManager::saveLog('log_sitime', "Updated problem category ID: {$problemCategory->id} swt.");
        
        return redirect()->route('master-data.problem-category.index')
            ->with('success', 'Problem category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProblemCategory $problemCategory)
    {
        // Check if category is being used by any laporan
        if ($problemCategory->laporan()->count() > 0) {
            return redirect()->route('master-data.problem-category.index')
                ->with('error', 'Cannot delete category that is being used by reports.');
        }

        $problemCategory->delete();

        \SharedManager::saveLog('log_sitime', "Deleted problem category ID: {$problemCategory->id} swt.");
        
        return redirect()->route('master-data.problem-category.index')
            ->with('success', 'Problem category deleted successfully.');
    }
}