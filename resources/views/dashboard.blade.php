@extends('layouts.app')

@section('content')
<div class="flex-1 p-6">
    <header class="flex items-center justify-between">
        <div class="relative w-64">
            <input class="w-full py-2 pl-10 pr-4 rounded-lg bg-gray-200 text-gray-700 focus:outline-none focus:bg-white focus:shadow-md" placeholder="Search ..." type="text"/>
            <i class="fas fa-search absolute left-3 top-3 text-gray-500"></i>
        </div>
        <div class="flex items-center md:hidden">
            <button id="menu-toggle" class="text-gray-500 focus:outline-none">
                <i class="fas fa-bars text-2xl"></i>
            </button>
        </div>
        <div class="hidden md:flex items-center">
            <div class="relative">
                <i class="fas fa-envelope text-gray-500 text-xl mr-4"></i>
                <i class="fas fa-bell text-gray-500 text-xl mr-4"></i>
                <span class="absolute top-0 right-0 bg-green-500 text-white text-xs px-1 rounded-full">4</span>
            </div>
            <div class="flex items-center">
                <img alt="User Avatar" class="w-10 h-10 rounded-full mr-2" height="40" src="https://storage.googleapis.com/a1aa/image/ctViH6zF6nuzgbLhLnqGcyKqn7xVHtdiqJtuMBsKlPI.jpg" width="40"/>
                <span class="text-gray-700">Hi, Hizrian</span>
            </div>
        </div>
    </header>
    <main class="mt-6">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-semibold text-gray-700">Dashboard</h1>
            <div class="flex items-center">
                <button class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg mr-2">Manage</button>
                <button class="bg-blue-500 text-white px-4 py-2 rounded-lg">Add Customer</button>
            </div>
        </div>
        <p class="text-gray-500 mt-2">Free Bootstrap 5 Admin Dashboard</p>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mt-6">
            <!-- Repite para cada tarjeta -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="flex items-center">
                    <i class="fas fa-users text-3xl text-blue-500 mr-4"></i>
                    <div>
                        <p class="text-gray-500">Visitors</p>
                        <p class="text-2xl font-semibold text-gray-700">1,294</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- Repite para las demás secciones -->
    </main>
</div>
@endsection