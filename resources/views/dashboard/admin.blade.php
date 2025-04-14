@extends('layouts.template')

@section('title', 'Dashboard Admin') {{-- Menambahkan title di tab browser --}}

@section('content')

<h1>Dashboard Admin</h1>
    <p>Selamat datang, {{ Auth::user()->username }}!</p>

@endsection