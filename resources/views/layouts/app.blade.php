<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Aurora')</title>

    <!-- Fuente Rubik -->
    <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@400;500;700&display=swap" rel="stylesheet">

    <!-- Estilos globales -->
    <style>
        :root {
            --color-primary: #113f67;
            --color-secondary: #34699A;
            --color-on-primary: #ffffff;
            --font-base: 'Rubik', sans-serif;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: var(--font-base);
            background-color: var(--color-primary);
            color: #333;
        }
