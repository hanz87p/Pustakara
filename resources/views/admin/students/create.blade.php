@extends('layouts.admin')

@section('title', 'Tambah Siswa')
@section('page-title', 'Tambah Siswa')

@section('content')
    <div class="max-w-2xl">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <form action="{{ route('admin.students.store') }}" method="POST">
                @include('admin.students._form', ['submitLabel' => 'Simpan Siswa'])
            </form>
        </div>
    </div>
@endsection