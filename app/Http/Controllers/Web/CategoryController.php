<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index() { return view('admin.categories.index'); }
    public function create() { return view('admin.categories.create'); }
    public function store(Request $request) { return redirect()->route('admin.categories.index'); }
    public function show($id) { return view('admin.categories.show', compact('id')); }
    public function edit($id) { return view('admin.categories.edit', compact('id')); }
    public function update(Request $request, $id) { return redirect()->route('admin.categories.index'); }
    public function destroy($id) { return redirect()->route('admin.categories.index'); }
}
