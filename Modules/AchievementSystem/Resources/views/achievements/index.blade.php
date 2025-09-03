@extends('layouts.app')

@section('title', 'Достижения')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">🏆 Система достижений</h1>
        <p class="text-gray-600">Зарабатывайте достижения и получайте очки за активность в системе</p>
    </div>

    @foreach($categories as $category)
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex items-center mb-4">
            @if($category->icon)
                <i class="{{ $category->icon }} text-2xl mr-3" style="color: {{ $category->color }}"></i>
            @endif
            <h2 class="text-xl font-semibold text-gray-800">{{ $category->name }}</h2>
        </div>
        
        @if($category->description)
            <p class="text-gray-600 mb-4">{{ $category->description }}</p>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($category->achievements as $achievement)
            <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-2">
                    @if($achievement->icon)
                        <i class="{{ $achievement->icon }} text-xl text-blue-600"></i>
                    @endif
                    <span class="text-sm font-medium text-gray-500">{{ $achievement->points }} очков</span>
                </div>
                
                <h3 class="font-semibold text-gray-800 mb-2">{{ $achievement->name }}</h3>
                <p class="text-sm text-gray-600">{{ $achievement->description }}</p>
                
                <div class="mt-3">
                    <a href="{{ route('achievements.show', $achievement) }}" 
                       class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        Подробнее →
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach
</div>
@endsection
