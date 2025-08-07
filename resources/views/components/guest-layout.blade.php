{{-- resources/views/components/guest-layout.blade.php --}}
@props(['title' => null, 'metaDescription' => null])

@php
    $pageTitle = $title ?? __('guest_layout.default_title');
    $pageDescription = $metaDescription ?? __('guest_layout.default_description');
@endphp

@extends('layouts.guest', [
    'title' => $pageTitle,
    'pageDescription' => $pageDescription,
])

@if (isset($heroFullWidth))
    @section('heroFullWidth')
        {{ $heroFullWidth }}
    @endsection
@endif

@if (isset($slot))
    @section('content')
        {{ $slot }}
    @endsection
@endif
