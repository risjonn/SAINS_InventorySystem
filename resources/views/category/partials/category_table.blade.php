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
                <button type="button" class="text-blue-500 hover:text-blue-700 mr-2"
                        data-bs-toggle="modal" data-bs-target="#editCategoryModal"
                        data-name="{{ $category->name }}"
                        data-active="{{ $category->active }}"
                        data-update-url="{{ route('category.update', $category->id) }}">
                        <i class="fas fa-edit"></i>
                </button>
                <button type="button" class="text-red-500 hover:text-red-700 delete-button"
                        data-action="{{ route('category.destroy', $category->id) }}">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="mt-4">
    {{ $categories->appends(request()->query())->links() }}
</div>