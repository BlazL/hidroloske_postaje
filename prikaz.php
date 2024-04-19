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

    <style>
        #map {
            height: 400px;
            width: 100%;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div>
        <!-- Ustvari div element v katerem bo prikazan zemljevid -->
        <div>
            <h1>Pregled - Hidrološka kontrola</h1>
        </div>
        <div id="map></div>
        <div>
            <h3>Izklopljeni senzori</h3>
            <table>
                <tr>
                    <th>Koda postaje</th>
                    <th>Reka</th>
                    <th>Merilno mesto</th>
                    <th>Ime kratko</th>
                    <th>Datum</th>
                </tr>
                <?php
                    // URL naslov XML file
                    $url = 'http://www.arso.gov.si/xml/vode/hidro_podatki_zadnji.xml';
                    // Naloži XML podatke iz URL naslova
                    $xml = simplexml_load_file($url);
                    // Zanka za vsak element 'postaja'
                    foreach ($xml->postaja as $postaja) {
                        $temperature = (string)$postaja->temp_vode;
                        // Preveri ali je temperatura null oz. empty
                        if (empty($temperature)) {
                            $stationCode = $postaja['sifra'];
                            $river = $postaja->reka;
                            $measurementPoint = $postaja->merilno_mesto;
                            $shortName = $postaja->ime_kratko;
                            $date = $postaja->datum;
                            // Prikaz podatkov v vrstici tabele
                            echo "<tr>";
                            echo "<td>$stationCode</td>";
                            echo "<td>$river</td>";
                            echo "<td>$measurementPoint</td>";
                            echo "<td>$shortName</td>";
                            echo "<td>$date</td>";
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
                $temperature = $postaja->temp_vode;
                // Dodaj marker in popup okence za vsako merilno postajo s podati o temperaturi
                echo "addMarker($lat, $lng, '$stationName<br>Temperatura vode: $temperature °C');\n";
            }
            ?>
        </script>

    </div>
</body>
</html>
