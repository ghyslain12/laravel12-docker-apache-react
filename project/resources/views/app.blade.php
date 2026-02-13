<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel + React</title>

    {{-- CRUCIAL : Doit être placé AVANT la directive @vite --}}
    @viteReactRefresh

    {{-- Charge ton point d'entrée React --}}
    @vite(['resources/crud-react-vite/src/main.tsx'])
</head>
<body>
{{-- L'ID doit correspondre à ce que cherche ton main.tsx (souvent 'root') --}}
<div id="root"></div>
</body>
</html>
