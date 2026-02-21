@extends('layouts.app')

@section('content')
<div class="container">
    <div class="alert alert-success">
        <h2>Test View is Working!</h2>
        <p>Categories count: {{ $categories->count() }}</p>
        <ul>
            @foreach($categories as $category)
                <li>{{ $category->id }}: {{ $category->name }}</li>
            @endforeach
        </ul>
    </div>
</div>
@endsection