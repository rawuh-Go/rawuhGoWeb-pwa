<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rawuh-Go</title>

    <link rel="icon" href="{{ asset('images/favicon.png') }}" type="image/png">

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="http://unpkg.com/leaflet/dist/leaflet.css">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        #map {
            height: 400px;
        }
    </style>
</head>

<body>
    {{ $slot }}
</body>

</html>