@extends('layouts.admin')

@section('title', 'Edit Buku')
@section('page-title', 'Edit Buku')

@section('content')

    <div class="max-w-3xl">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <form action="{{ route('admin.books.update', $book) }}" method="POST" enctype="multipart/form-data">
                @method('PUT')
                @include('admin.books._form', ['submitLabel' => 'Update Buku'])
            </form>
        </div>
    </div>

@endsection