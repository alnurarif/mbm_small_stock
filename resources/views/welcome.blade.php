<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" id="token" content="{{ csrf_token() }}">
        <title>Small Store</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
        @vite('resources/css/app.css')
        <script>window.Laravel = {csrfToken: '{{ csrf_token() }}'}</script>
    </head>
    <body class="antialiased">
        <div id="app">

        </div>
        @vite('resources/js/app.js')
    </body>
</html>
