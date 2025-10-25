@extends('errors.layout')

@section('title', __('Prohibited'))

@section('code', '403') {{-- Los '0' se reemplazarán automáticamente por la imagen auroralogo.png --}}

@section('message', $exception->getmessage() ?: 'Forbidden')
