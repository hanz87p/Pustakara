@extends('layouts.admin')

@section('title', 'Tambah Buku')
@section('page-title', 'Tambah Buku')

@section('content')

    <div class="max-w-3xl">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <form action="{{ route('admin.books.store') }}" method="POST" enctype="multipart/form-data">
                @include('admin.books._form', ['submitLabel' => 'Simpan Buku'])
            </form>
        </div>
    </div>

@endsection