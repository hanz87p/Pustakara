@extends('layouts.admin')

@section('title', 'Edit Siswa')
@section('page-title', 'Edit Siswa')

@section('content')
    <div class="max-w-2xl">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <form action="{{ route('admin.students.update', $student) }}" method="POST">
                @method('PUT')
                @include('admin.students._form', ['submitLabel' => 'Update Siswa'])
            </form>
        </div>
    </div>
@endsection