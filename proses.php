<?php
    //koneksi
    $conn = pg_connect("host=localhost port=5432 dbname=mydatabase user=postgres password=863957");

    //set variabel
    $coords = $_POST['coords'];
    // format koordinat harus dalam bentuk "POINT(longitude latitude)"
    $point = 'LINESTRING(' . ($coords) . ')';
    $geom = "ST_GeomFromText('$point')";
    
    
    echo $point;
    echo $geom;
    
    //input data
    $insert = pg_query($conn, "INSERT INTO polyline (geo) VALUES ($geom)");


    //menampilkan pesan kesalahan jika terjadi error
    if (!$insert) {
        echo "Error: " . pg_last_error($conn);
        exit();
    }

    //kembali
    header("Location: index.php");
