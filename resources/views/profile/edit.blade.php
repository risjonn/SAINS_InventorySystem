@extends('layouts.layout')

@section('title', 'Profile')

@section('content')
<div class="border-b mb-4">
    <ul class="flex">
        <li class="flex-1">
            <a href="#profile" class="tab-link block text-center px-4 py-2 border-b-4 border-transparent rounded-t-lg text-gray-600 hover:text-blue-600 hover:border-blue-600">
                Profile Information
            </a>
        </li>
        <li class="flex-1">
            <a href="#password" class="tab-link block text-center px-4 py-2 border-b-4 border-transparent rounded-t-lg text-gray-600 hover:text-blue-600 hover:border-blue-600">
                Update Password
            </a>
        </li>
        <li class="flex-1">
            <a href="#delete" class="tab-link block text-center px-4 py-2 border-b-4 border-transparent rounded-t-lg text-gray-600 hover:text-blue-600 hover:border-blue-600">
                Delete Account
            </a>
        </li>
    </ul>
</div>

<div id="profile" class="tab-content">
    @include('profile.partials.update-profile-information-form')
</div>
<div id="password" class="tab-content hidden">
    @include('profile.partials.update-password-form')
</div>
<div id="delete" class="tab-content hidden">
    @include('profile.partials.delete-user-form')
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Function to activate a tab based on the given hash
    function activateTab(hash) {
        // Hide all tab contents
        document.querySelectorAll('.tab-content').forEach(function(tabContent) {
            tabContent.classList.add('hidden');
        });
        // Remove active styling from all tab links
        document.querySelectorAll('.tab-link').forEach(function(tabLink) {
            tabLink.classList.remove('text-blue-600', 'border-blue-600');
            tabLink.classList.add('text-gray-600', 'border-transparent');
        });
        // Show the tab corresponding to the hash
        var activeTab = document.querySelector(hash);
        if (activeTab) {
            activeTab.classList.remove('hidden');
        }
        // Mark the corresponding link as active
        var activeLink = document.querySelector('.tab-link[href="' + hash + '"]');
        if (activeLink) {
            activeLink.classList.add('text-blue-600', 'border-blue-600');
            activeLink.classList.remove('text-gray-600', 'border-transparent');
        }
    }

    // Determine the default active tab:
    var defaultHash = '#profile';
    @if(session('activeTab'))
        defaultHash = "#{{ session('activeTab') }}";
    @else
        if (window.location.hash) {
            defaultHash = window.location.hash;
        }
    @endif

    activateTab(defaultHash);

    // Add click event listeners to tab links to update the hash and active tab
    document.querySelectorAll('.tab-link').forEach(function(link) {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            var hash = this.getAttribute('href');
            window.location.hash = hash; // update URL hash
            activateTab(hash);
        });
    });
});
</script>
@endsection
