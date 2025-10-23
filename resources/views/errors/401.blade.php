@extends('errors.layout')

@section('title', __('Not Found'))

@section('code', '401') 

@section('message', $exception->getmessage() ?: 'Not Found')
