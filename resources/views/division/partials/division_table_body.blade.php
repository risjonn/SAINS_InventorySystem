@foreach($divisions as $division)
    <tr class="border-b">
        <td class="p-2">{{ $division->name }}</td>
        <td class="p-2 {{ $division->active ? 'text-green-500 font-bold' : 'text-red-500 font-bold' }}">
            {{ $division->active ? 'Active' : 'Inactive' }}
        </td>
        <td class="p-2">
            <button type="button" class="text-blue-500 hover:text-blue-700 mr-2"
                    data-bs-toggle="modal" data-bs-target="#editDivisionModal"
                    data-name="{{ $division->name }}"
                    data-active="{{ $division->active }}"
                    data-update-url="{{ route('division.update', $division->id) }}">
                    <i class="fas fa-edit"></i>
            </button>
            <button type="button" class="text-red-500 hover:text-red-700 delete-button"
                    data-action="{{ route('division.destroy', $division->id) }}"
                    data-type="division">
                <i class="fas fa-trash-alt"></i>
            </button>
        </td>
    </tr>
@endforeach