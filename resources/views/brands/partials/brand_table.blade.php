<table class="min-w-full mt-4 border rounded-lg shadow-md bg-white">
    <thead>
        <tr class="bg-gray-200 text-left">
            <th class="p-2">Brand Name</th>
            <th class="p-2">Status</th>
            <th class="p-2">Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach($brands as $brand)
        <tr class="border-b">
            <td class="p-2">{{ $brand->name }}</td>
            <td class="p-2">{{ $brand->active ? 'Active' : 'Inactive' }}</td>
            <td class="p-2">
                <button type="button" class="text-blue-500 hover:text-blue-700 mr-2"
                        data-bs-toggle="modal" data-bs-target="#editBrandModal"
                        data-name="{{ $brand->name }}"
                        data-active="{{ $brand->active }}"
                        data-update-url="{{ route('brands.update', $brand->id) }}">
                        <i class="fas fa-edit"></i>
                </button>
                <button type="button" class="text-red-500 hover:text-red-700 delete-button"
                        data-action="{{ route('brands.destroy', $brand->id) }}">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="mt-4">
    {{ $brands->appends(request()->query())->links() }}
</div>
