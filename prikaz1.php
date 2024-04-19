<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zemljevid</title>
    <!-- Vključi Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <!-- Vključi Leaflet JavaScript -->
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        #map {
            height: 400px;
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="p-8">
        <!-- Ustvari div element v katerem bo prikazan zemljevid -->
        <div>
            <h1 class="text-center text-2xl">Pregled - Hidrološka kontrola</h1>
        </div>
        <div id="map" class="mt-8"></div>
        <div>
            <h3 class="text-center text-xl mt-8">Izklopljeni senzori</h3>
            <table class="table-auto w-full mt-4 bg-white shadow-md rounded-lg overflow-hidden">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="px-4 py-2">Koda postaje</th>
                        <th class="px-4 py-2">Reka</th>
                        <th class="px-4 py-2">Merilno mesto</th>
                        <th class="px-4 py-2">Ime kratko</th>
                        <th class="px-4 py-2">Datum</th>
                    </tr>
                </thead>
                <?php
                    // URL of the XML file
                    $url = 'http://www.arso.gov.si/xml/vode/hidro_podatki_zadnji.xml';
                    // Load the XML data from the URL
                    $xml = simplexml_load_file($url);
                    // Loop through each 'postaja' element
                    foreach ($xml->postaja as $postaja) {
                        $temperature = (string)$postaja->temp_vode;
                        // Check if the temperature is null or empty
                        if (empty($temperature)) {
                            $stationCode = $postaja['sifra'];
                            $river = $postaja->reka;
                            $measurementPoint = $postaja->merilno_mesto;
                            $shortName = $postaja->ime_kratko;
                            $date = $postaja->datum;
                            // Output the station details in a table row
                            echo "<tr>";
                            echo "<td class='px-4 py-2 border-b border-gray-300'>$stationCode</td>";
                            echo "<td class='px-4 py-2 border-b border-gray-300'>$river</td>";
                            echo "<td class='px-4 py-2 border-b border-gray-300'>$measurementPoint</td>";
                            echo "<td class='px-4 py-2 border-b border-gray-300'>$shortName</td>";
                            echo "<td class='px-4 py-2 border-b border-gray-300'>$date</td>";
                            echo "</tr>";
                        }
                    }
                ?>
            </table>
        </div>

        <script>
            // Inicializacija Leaflet zemljevida
            var map = L.map('map').setView([46.1, 14.8], 9); // Privzeti center in zoom zemljevida

            // Dodaj OpenStreetMap sloj
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
            }).addTo(map);

            // Definiraj funkcijo za dodajanje markerjev na zemljevid
            function addMarker(lat, lng, popupContent) {
                var marker = L.marker([lat, lng]).addTo(map);
                if (popupContent) {
                    marker.bindPopup(popupContent);
                }
            }

            <?php
            // URL pot do XML datoteke
            $url = 'http://www.arso.gov.si/xml/vode/hidro_podatki_zadnji.xml';
            // Naloži XML datoteko
            $xml = simplexml_load_file($url);
            // Zanka za vsak 'postaja' element
            foreach ($xml->postaja as $postaja) {
                $lat = (float)$postaja['ge_sirina'];
                $lng = (float)$postaja['ge_dolzina'];
                $stationName = $postaja->ime_kratko;
                $temperature = ($postaja->temp_vode != '') ? $postaja->temp_vode.'°C' : '/';
                // Dodaj marker in popup okence za vsako merilno postajo s podati o temperaturi
                echo "addMarker($lat, $lng, '$stationName<br>Temperatura vode: $temperature');\n";
            }
            ?>
        </script>

    </div>
</body>
</html>
