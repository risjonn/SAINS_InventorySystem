@extends('layouts.layout')
@section('content')
  <div class="container mt-5">
    <!-- Calling with auto-generated message using resource type "user" -->
    <button class="btn btn-primary" 
      onclick="showGlobalSuccessModal('created', '', 'user')">
      Test Created Success Modal (User)
    </button>
    
    <!-- Calling with auto-generated message using resource type "item" for update -->
    <button class="btn btn-primary" 
      onclick="showGlobalSuccessModal('updated', '', 'item')">
      Test Updated Success Modal (Item)
    </button>
    
    <!-- Test for "Delete User" -->
    <button class="btn btn-warning" 
      onclick="showDeleteConfirmationModal('user', function(itemType) { 
          // After deletion, show success modal dynamically:
          showGlobalSuccessModal('deleted', '', itemType);
        })">
      Test Delete Confirmation Modal (User)
    </button>

    <!-- Test for "Delete Brand" -->
    <button class="btn btn-warning mt-3" 
      onclick="showDeleteConfirmationModal('brand', function(itemType) { 
          // After deletion, show success modal dynamically:
          showGlobalSuccessModal('deleted', '', itemType);
        })">
      Test Delete Confirmation Modal (Brand)
    </button>
  </div>
@endsection
