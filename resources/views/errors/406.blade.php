@extends('errors.layout')

@section('title', __('Unacceptable'))

@section('code', '406') 

@section('message', $exception->getmessage() ?: 'Solicitud inaceptable')
