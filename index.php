<!DOCTYPE html>
<html lang="en">


<!-- leaflet css  -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A==" crossorigin="" />

<!-- bootstrap cdn  -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Peta</title>
  <style>
    /* ukuran peta */
    #mapid {
      height: 100vh;
    }

    .text-area {
      background-color: #f2f2f2;
      padding: 20px;
      border: 1px solid #ccc;
      border-radius: 10px;
      font-size: 16px;
      line-height: 1.5;
      color: #333;
      text-align: justify;
      margin-bottom: 20px;
      width: 100%;
      height: 150px;
    }

    .jumbotron {
      height: 100%;
      border-radius: 0;
    }

    body {
      background-color: #ebe7e1;
    }
  </style>
</head>


<!-- leaflet js  -->
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js" integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA==" crossorigin=""></script>

<body>
  <div class="row"> <!-- class row digunakan sebelum membuat column  -->
    <div class="col-4"> <!-- ukuruan layar dengan bootstrap adalah 12 kolom, bagian kiri dibuat sebesar 4 kolom-->
      <div class="jumbotron"> <!-- untuk membuat semacam container berwarna abu -->
        <h1>Sistem Informasi Geografis</h1>
        <hr>
        <form class="area" method="POST" action="proses.php">
          <label for="coords">Koordinat:</label><br>
          <input type="text" name="coords" id="coords" class="text-area" placeholder="Longitude Latitude"><br>
          <input class="btn btn-info" type="submit" value="Submit">
        </form>
      </div>
    </div>
    <div class="col-8"> <!-- ukuruan layar dengan bootstrap adalah 12 kolom, bagian kiri dibuat sebesar 4 kolom-->
      <!-- peta akan ditampilkan dengan id = mapid -->
      <div id="mapid"></div>
    </div>
  </div>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/leaflet.js"></script>
  <script>
    var mymap = L.map('mapid').setView([-7.0529210783224485, 113.38176484784677], 10);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, ' +
        '<a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
      maxZoom: 20,
    }).addTo(mymap);
    var bigMarker = L.icon({
      iconUrl: 'https://cdn.rawgit.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
      iconSize: [18, 27],
      iconAnchor: [10, 24],
      popupAnchor: [1, -10],
      shadowSize: [17, 17]
    });

    var polyline = L.polyline([], {
      color: 'red'
    }).addTo(mymap);

    var coordsTextArea = document.getElementById('coords');
    var coords = [];
    var markers = []; // Menyimpan marker yang telah dibuat

    mymap.on('click', function(e) {
      var latLng = e.latlng;
      coords.push(latLng);

      // Menambahkan marker pada setiap titik koordinat
      var marker = L.marker(latLng, {
        icon: bigMarker
      }).addTo(mymap);
      markers.push(marker);

      polyline.setLatLngs(coords);
      coordsTextArea.value = coords.map(function(coord) {
        return coord.lng + " " + coord.lat;
      }).join(',');
    });

    // Menambahkan fungsi untuk menghapus marker pada klik kanan
    mymap.on('contextmenu', function(e) {
      // Mencari index marker yang di-klik
      var index = markers.findIndex(function(marker) {
        return marker.getLatLng().equals(e.latlng);
      });

      if (index !== -1) {
        // Menghapus marker dari map dan array markers
        markers[index].remove();
        markers.splice(index, 1);

        // Menghapus titik koordinat dari array coords dan mengupdate polyline
        coords.splice(index, 1);
        polyline.setLatLngs(coords);

        coordsTextArea.value = coords.map(function(coord) {
          return coord.lng + " " + coord.lat;
        }).join(',');
      }
    });

    // menambahkan fungsi untuk mereset variabel coords
    function resetCoords() {
      polyline.setLatLngs([]);
      coords = [];
      coordsTextArea.value = "";
    }
    <?php
    $dbhost = 'localhost';
    $dbname = 'mydatabase';
    $dbuser = 'postgres';
    $dbpass = '863957';
    $pdo = new PDO("pgsql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);

    // Mengambil data polyline dari tabel polyline
    $query = "SELECT ST_AsGeoJSON(geo) as geojson FROM polyline";
    $result = $pdo->query($query);

    // Inisialisasi variabel layers dan counter
    $layers = [];
    $counter = 0;

    // looping data menggunakan while
    while ($row = $result->fetch()) {
      // Mengubah GeoJSON menjadi objek PHP dengan fungsi json_decode()
      $geometry = json_decode($row['geojson']);

      // Jika tipe geometri adalah LineString, tambahkan ke variabel polyline
      if ($geometry->type == 'LineString') {
        $coordinates = $geometry->coordinates;
        $polyline = [];
        for ($i = 0; $i < count($coordinates); $i++) {
          $lat = $coordinates[$i][1];
          $lng = $coordinates[$i][0];
          $polyline[] = "[$lat, $lng]";
        }
        // Menambahkan polyline ke dalam variabel layers sebagai layer yang terpisah-pisah
        $layers[] = "var layer$counter = L.polyline([" . implode(",", $polyline) . "], {color: 'red'}).addTo(mymap);";
        $counter++;
      }
    }

    // Menampilkan semua layer pada Leaflet
    echo implode("\n", $layers);
    ?>
  </script>
</body>

</html>