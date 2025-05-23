@foreach($categories as $category)
    <tr class="border-b">
        <td class="p-2">{{ $category->name }}</td>
        <td class="p-2 {{ $category->active ? 'text-green-500 font-bold' : 'text-red-500 font-bold' }}">
            {{ $category->active ? 'Active' : 'Inactive' }}
        </td>
        <td class="p-2">
     <button
  type="button"
  class="text-blue-500 hover:text-blue-700 mr-2"
  data-bs-toggle="modal"
  data-bs-target="#editCategoryModal"
  data-name="{{ $category->name }}"
  data-active="{{ $category->active ? 1 : 0 }}"
  data-is-bulk="{{ $category->is_bulk ? 1 : 0 }}"
  data-update-url="{{ route('category.update', $category->id) }}"
>
  <i class="fas fa-edit"></i>
</button>
            <button type="button" class="text-red-500 hover:text-red-700 delete-button"
                    data-action="{{ route('category.destroy', $category->id) }}"
                    data-type="category">
                <i class="fas fa-trash-alt"></i>
            </button>
        </td>
    </tr>
@endforeach