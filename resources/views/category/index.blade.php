@extends('layouts.layout')

@section('title', 'Manage Categories')

@section('content')
<div class="container mx-auto px-4 my-5">
    <h2 class="text-xl font-semibold mb-4">Manage Categories</h2>

    <!-- Button to trigger Create Category modal -->
    <button type="button" class="bg-green-600 text-white px-4 py-2 rounded mb-4"
            data-bs-toggle="modal" data-bs-target="#createCategoryModal">
        <i class="fas fa-plus mr-1"></i> Add New Category
    </button>

    <!-- Categories Listing Table -->
    <table class="min-w-full mt-4 border rounded-lg shadow-md bg-white">
        <thead>
            <tr class="bg-gray-200 text-left">
                <th class="p-2">Category Name</th>
                <th class="p-2">Status</th>
                <th class="p-2">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($categories as $category)
            <tr class="border-b">
                <td class="p-2">{{ $category->name }}</td>
                <td class="p-2">{{ $category->active ? 'Active' : 'Inactive' }}</td>
                <td class="p-2">
                    <!-- Edit Button with data attributes -->
                    <button type="button" class="text-blue-500 hover:text-blue-700 mr-2"
                        data-bs-toggle="modal" data-bs-target="#editCategoryModal"
                        data-name="{{ $category->name }}"
                        data-active="{{ $category->active }}"
                        data-update-url="{{ route('category.update', $category->id) }}">
                        <i class="fas fa-edit"></i>
                    </button>
                    <!-- Delete Button triggers the Delete Modal -->
                    <button type="button" class="text-red-500 hover:text-red-700 delete-button"
                        data-action="{{ route('category.destroy', $category->id) }}">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $categories->links() }}
    </div>
</div>

<form id="globalDeleteForm" method="POST" style="display: none;">
  @csrf
  @method('DELETE')
</form>

@include('category.partials.create-modal')
@include('category.partials.edit-modal')

@endsection
