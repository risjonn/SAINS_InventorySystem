@extends('layouts.layout')

@section('title', "Low/Critical SKUs in {$category} - {$division->name}")

@section('content')
<div class="container mx-auto p-4">
  <div class="flex items-center justify-between mb-4">
    <div>
      <a href="{{ route('stocks.low', ['division' => $division->id]) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg transition-colors duration-150 mb-2">
        <i class="fas fa-arrow-left mr-2"></i>Summary By Category
      </a>
      <h1 class="text-2xl font-bold mb-1">Division: {{ $division->name }}</h1>
    </div>
  </div>

  {{-- Filter & Search --}}
  <form method="GET" class="flex flex-wrap items-center gap-4 mb-6">
    <input
      type="text"
      name="q"
      value="{{ request('q') }}"
      placeholder="Search product…"
      class="border rounded px-3 py-2 w-1/3"
    >

    <div class="relative">
      <select name="status" class="appearance-none bg-white border rounded px-3 py-2 pr-8">
        <option value="">All statuses</option>
        <option value="low"      {{ request('status')==='low'      ? 'selected':'' }}>Low</option>
        <option value="critical" {{ request('status')==='critical' ? 'selected':'' }}>Critical</option>
      </select>
    </div>

    <div class="relative">
      <select name="perPage" class="appearance-none bg-white border rounded px-3 py-2 pr-8">
        <option value="20" {{ request('perPage')==20 ? 'selected':'' }}>20 / page</option>
        <option value="30" {{ request('perPage')==30 ? 'selected':'' }}>30 / page</option>
        <option value="50" {{ request('perPage')==50 ? 'selected':'' }}>50 / page</option>
      </select>
    </div>

    <button type="submit" class="bg-blue-600 text-white rounded px-4 py-2">Apply</button>
  </form>

  {{-- SKU Table --}}
  <div class="mt-4 bg-white rounded-xl shadow-lg p-6">
    <table class="table-fixed w-full mb-6">
      <h2 class="text-xl font-semibold text-gray-600 mb-4">Category: {{ $category }}</h2>
      <thead class="bg-gray-100">
        <tr class="bg-gray-300">
          <th class="px-4 py-2 text-left w-1/3">
            @php
              $isActive = request('sort') === 'name';
              $dir      = request('dir')  === 'asc' ? 'asc' : 'desc';
            @endphp
            <a href="?{{ http_build_query(array_merge(request()->all(), [
                  'sort' => 'name',
                  'dir'  => $isActive && $dir==='asc' ? 'desc' : 'asc'
            ])) }}"
              class="inline-flex items-center space-x-1">
              <span>Product</span>
              @if($isActive)
                <i class="fas fa-sort-{{ $dir === 'asc' ? 'up' : 'down' }}"></i>
              @else
                <i class="fas fa-sort text-gray-400"></i>
              @endif
            </a>
          </th>
          <th class="px-4 py-2 text-center w-1/12">
            @php
              $isActive = request('sort') === 'qty';
              $dir      = request('dir')  === 'asc' ? 'asc' : 'desc';
            @endphp
            <a href="?{{ http_build_query(array_merge(request()->all(), [
                  'sort' => 'qty',
                  'dir'  => $isActive && $dir==='asc' ? 'desc' : 'asc'
            ])) }}"
              class="inline-flex items-center space-x-1">
              <span>Qty</span>
              @if($isActive)
                <i class="fas fa-sort-{{ $dir === 'asc' ? 'up' : 'down' }}"></i>
              @else
                <i class="fas fa-sort text-gray-400"></i>
              @endif
            </a>
          </th>
          <th class="px-4 py-2 text-center w-1/6">Status</th>
          <th class="px-4 py-2 text-center w-1/4">Thresholds</th>
          <th class="px-4 py-2 text-center w-1/6">Action</th>
        </tr>
      </thead>
      <tbody>
        @forelse($paginator as $row)
          @php
            $product = $row['product'];
          @endphp
          <tr 
            data-product-id="{{ $product->id }}"
            data-user-id="{{ $selectedAdmin->id }}"
            class="bg-white even:bg-gray-100 hover:bg-gray-200">
            <td class="px-4 py-2 text-left">
              {{ $product->brand }} {{ $product->model }}
            </td>

            <td class="px-4 py-2 text-center">{{ $row['qty'] }}</td>

            <td class="px-4 py-2 text-center">
              @if($row['status'] === 'critical')
                <span class="inline-flex items-center bg-red-100 text-red-700 px-2 py-1 rounded-full text-xs font-semibold">
                  <i class="fas fa-exclamation-triangle mr-1"></i> Critical
                </span>
              @elseif($row['status'] === 'low')
                <span class="inline-flex items-center bg-amber-100 text-amber-700 px-2 py-1 rounded-full text-xs font-semibold">
                  <i class="fas fa-exclamation-circle mr-1"></i> Low
                </span>
              @elseif($row['status'] === 'ok')
                <span class="inline-flex items-center bg-green-100 text-green-700 px-2 py-1 rounded-full text-xs font-semibold">
                  <i class="fas fa-check-circle mr-1"></i> Ok
                </span>
              @endif
            </td>

            {{-- 1) Threshold values & inline form --}}
            <td class="px-4 py-2 text-center threshold-cell">
              <span class="threshold-display block w-full">
                Low: <strong>{{ $row['low_threshold'] }}</strong>,
                Crit: <strong>{{ $row['critical_threshold'] }}</strong>
              </span>
              <div class="threshold-form hidden w-full flex justify-center items-center space-x-2">
                <div class="flex items-center">
                  <label class="text-xs mr-1">Low:</label>
                  <input type="number" min="0" max="30" class="low-input w-16 border rounded px-1 py-0.5 text-sm"
                        value="{{ $row['low_threshold'] }}">
                </div>
                <div class="flex items-center">
                  <label class="text-xs mr-1">Crit:</label>
                  <input type="number" min="0" max="20" class="crit-input w-16 border rounded px-1 py-0.5 text-sm"
                        value="{{ $row['critical_threshold'] }}">
                </div>
              </div>
            </td>

            {{-- 2) Actions: Edit / Save / Cancel --}}
            <td class="px-4 py-2 text-center action-cell">
              <div class="flex justify-center space-x-2">
                <button type="button"
                        class="edit-threshold text-blue-600 hover:underline text-xs">
                  Edit
                </button>
                <button type="button"
                        class="save-threshold hidden bg-green-500 hover:bg-green-600 text-white text-xs rounded px-2 py-1">
                  Save
                </button>
                <button type="button"
                        class="cancel-threshold hidden text-gray-600 hover:text-gray-800 text-xs">
                  Cancel
                </button>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="px-4 py-6 text-center text-gray-500">
              No SKUs match your criteria.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>

    {{-- Pagination Links --}}
    <div class="mt-4">
      {{ $paginator->appends(request()->all())->links() }}
    </div>
    
  </div>
</div>
@endsection

@section('scripts')
  <script>
    // Toast notification system
    function showToast(message, type = 'success') {
      // Remove any existing toast
      const existingToast = document.getElementById('toast-notification');
      if (existingToast) {
        existingToast.remove();
      }
      
      // Create toast container if it doesn't exist
      let toastContainer = document.getElementById('toast-container');
      if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toast-container';
        toastContainer.style.cssText = 'position: fixed; top: 1rem; right: 1rem; z-index: 9999;';
        document.body.appendChild(toastContainer);
      }
      
      // Create new toast
      const toast = document.createElement('div');
      toast.id = 'toast-notification';
      
      // Set toast styles based on type
      let bgColor, textColor, icon;
      switch(type) {
        case 'success':
          bgColor = '#10B981'; // Green
          textColor = 'white';
          icon = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>';
          break;
        case 'error':
          bgColor = '#EF4444'; // Red
          textColor = 'white';
          icon = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>';
          break;
        default:
          bgColor = '#3B82F6'; // Blue
          textColor = 'white';
          icon = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" /></svg>';
      }
      
      // Apply styles to toast
      toast.style.cssText = `
        display: flex;
        align-items: center;
        padding: 0.75rem 1rem;
        border-radius: 0.375rem;
        margin-bottom: 0.75rem;
        background-color: ${bgColor};
        color: ${textColor};
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        transform: translateX(100%);
        opacity: 0;
        transition: transform 0.3s ease-out, opacity 0.3s ease-out;
        max-width: 24rem;
        overflow: hidden;
      `;
      
      // Toast content
      toast.innerHTML = `
        <div class="mr-2" style="width: 20px; height: 20px;">
          ${icon}
        </div>
        <div style="flex-grow: 1;">${message}</div>
        <button type="button" class="toast-close-btn ml-4" style="background: transparent; border: none; color: inherit; cursor: pointer; padding: 0;">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
          </svg>
        </button>
      `;
      
      // Add to container
      toastContainer.appendChild(toast);
      
      // Trigger animation
      setTimeout(() => {
        toast.style.transform = 'translateX(0)';
        toast.style.opacity = '1';
      }, 10);
      
      // Auto dismiss
      const dismissTimeout = setTimeout(() => {
        dismissToast(toast);
      }, 3000);
      
      // Close button handler
      const closeBtn = toast.querySelector('.toast-close-btn');
      closeBtn.addEventListener('click', () => {
        clearTimeout(dismissTimeout);
        dismissToast(toast);
      });
    }

    function dismissToast(toast) {
      toast.style.transform = 'translateX(100%)';
      toast.style.opacity = '0';
      
      setTimeout(() => {
        toast.remove();
      }, 300);
    }

    // Check for flash message from localStorage and display it
    document.addEventListener('DOMContentLoaded', function() {
      const flashMessage = localStorage.getItem('flashMessage');
      const flashType = localStorage.getItem('flashType');
      
      if (flashMessage) {
        showToast(flashMessage, flashType || 'success');
        
        // Clear the stored message
        localStorage.removeItem('flashMessage');
        localStorage.removeItem('flashType');
      }
    });

    // once and for all, listen to clicks on the table
    document.addEventListener('click', async function(e) {
      // make sure we only react inside our SKU detail table
      const row = e.target.closest('tr[data-product-id]');
      if (!row) return;

      const cell    = row.querySelector('.threshold-cell');
      const actions = row.querySelector('.action-cell');
      const disp    = cell.querySelector('.threshold-display');
      const form    = cell.querySelector('.threshold-form');
      const pid     = row.dataset.productId;
      
      // Get the user ID from the data attribute
      const userId  = row.dataset.userId;

      // 1) Edit button
      if (e.target.matches('.edit-threshold')) {
        disp.classList.add('hidden');
        form.classList.remove('hidden');
        actions.querySelector('.edit-threshold').classList.add('hidden');
        actions.querySelector('.save-threshold').classList.remove('hidden');
        actions.querySelector('.cancel-threshold').classList.remove('hidden');
        return;
      }

      // 2) Cancel button
      if (e.target.matches('.cancel-threshold')) {
        form.classList.add('hidden');
        disp.classList.remove('hidden');
        actions.querySelector('.edit-threshold').classList.remove('hidden');
        actions.querySelector('.save-threshold').classList.add('hidden');
        actions.querySelector('.cancel-threshold').classList.add('hidden');
        return;
      }

      // 3) Save button
      if (e.target.matches('.save-threshold')) {
        const btn  = e.target;
        const low  = form.querySelector('.low-input').value;
        const crit = form.querySelector('.crit-input').value;

        btn.disabled = true;
        try {
          const res = await fetch(`{{ url('thresholds') }}/${pid}`, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ 
              low, 
              critical: crit, 
              user_id: userId // Now properly defined
            })
          });
          const json = await res.json();
          if (json.success) {
            // Get product name for the success message
            const product = row.querySelector('td:first-child').textContent.trim();
            
            // Store message in localStorage to display after refresh
            localStorage.setItem('flashMessage', `Thresholds updated successfully for ${product}`);
            localStorage.setItem('flashType', 'success');
            
            // Refresh the page
            window.location.reload();
          } else {
            // Show error toast without page refresh
            showToast('Failed to save thresholds', 'error');
            btn.disabled = false;
          }
        } catch (err) {
          console.error(err);
          // Show error toast without page refresh
          showToast('Error saving thresholds', 'error');
          btn.disabled = false;
        }
      }
    });
  </script>
@endsection