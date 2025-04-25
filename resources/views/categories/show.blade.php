@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-semibold mb-6">Detalles de la Categoría</h1>
    <div class="bg-white rounded-lg shadow-md p-6">
        <p><strong>Nombre:</strong> {{ $category->name }}</p>
        <p><strong>Icono:</strong> {{ $category->icon }}</p>
        <a href="{{ route('categorys.index') }}" class="bg-blue-500 text-white px-4 py-2 rounded-lg mt-4 inline-block">Volver</a>
    </div>
</div>
@endsection