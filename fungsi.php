<?php 

// koneksi ke database
$conn = mysqli_connect("localhost","root","","dramagon");

function daftar($data) {
    global $conn;

    $username = stripslashes($data["uname"]);
    $password = $data["password"];
    $k_password = $data["k_password"];
    $email = stripslashes($data["email"]);

    if ( strlen($username) > 10 ) {
        echo "<script>
            alert('Maksimal 10 karakter!')
        </script>";
        return false;
    }
    // cek username yg sudah dipakai
    $result = mysqli_query($conn, "SELECT username FROM pengguna WHERE username = '$username'");

    if( mysqli_fetch_assoc($result) ) {
        echo "<script>
                alert('username sudah dipakai!')
            </script>";
        return false;
    }

    // cek konfirmasi password
    if ($password !== $k_password) {
        echo "<script>
                alert('konfirmasi password tidak sesuai!');
              </script>";
        return false;
    }

    // enkripsi password
    $password = password_hash($password, PASSWORD_DEFAULT);

    // tambahkan userbaru ke database
    mysqli_query($conn, "INSERT INTO pengguna VALUES(
        '','$username','$password', '$username','',
        '$email','','','')");
    
    return mysqli_affected_rows($conn);
}

function masuk($data) {
    global $conn;

    $username = stripslashes($_POST["username"]);
    $password = $_POST["password"];

    $result = mysqli_query($conn, "SELECT * FROM pengguna WHERE username = '$username'");

    // cek username
    if ( mysqli_num_rows($result) === 1 ) {
        // cek password
        $row = mysqli_fetch_assoc($result);
        if ( password_verify($password, $row["password"]) ) {
            return 1;
        } else {
            echo "<script>
                 alert('password salah!');
                </script>";
            return false;
        }
    } else {
        echo "<script>
                 alert('Username tidak ada!');
                </script>";
            return false;
    }
}

function edit($data) {
    global $conn;

    $id_pengguna = $data["id_pengguna"];
    $username = stripslashes($data["username"]);
    $username_lama = $data["username_lama"];
    $nama = stripslashes($data["nama"]);
    $email = stripslashes($data["email"]);
    $telpon = stripslashes($data["notelp"]);
    $jenkel = stripslashes($data["jk"]);
    $tanggalLahir = $data["tl"]; 

    // cek username yg sudah dipakai
    $result = mysqli_query($conn, "SELECT username FROM pengguna WHERE username = '$username'");
    if ($username == $username_lama) {
        $cek = 1;
    } else {
        $cek = 0;
    }
    if ($cek == 1) {
        // update pengguna ke database
         mysqli_query($conn, "UPDATE pengguna SET
            nama = '$nama',
            email = '$email',
            telpon = '$telpon',
            jenkel = '$jenkel',
            tanggalLahir = '$tanggalLahir'
        WHERE id_pengguna = $id_pengguna");
        return mysqli_affected_rows($conn);
    } else if ($cek == 0) {
        if(mysqli_num_rows($result)) {
            echo "<script>
                alert('username sudah dipakai!')
                </script>";
            return false;
        } else {
            // update pengguna ke database
             mysqli_query($conn, "UPDATE pengguna SET
                username = '$username',
                nama = '$nama',
                email = '$email',
                telpon = '$telpon',
                jenkel = '$jenkel',
                tanggalLahir = '$tanggalLahir'
            WHERE id_pengguna = $id_pengguna");
            return mysqli_affected_rows($conn);
        }
    }
}

function buatForum($data) {
    global $conn;

    if (!empty($_FILES['image']['tmp_name'])) {
        $file = $_FILES['image']['tmp_name'];
        if (!isset($file) ){
            echo "<script>alert('Pilih file gambar');</script>";
            return false;
        }

        else {
            $image = addslashes(file_get_contents($_FILES['image']['tmp_name']));
            $image_name = addslashes($_FILES['image']['name']);
            $image_size = getimagesize($_FILES['image']['tmp_name']);


            if ($image_size == false) {
                echo "<script>alert('File yang dipilih bukan gambar');</script>";
                return false;
            }
        }
    } else {
        $image = NULL;
    }
    
    $judul_forum = stripslashes($data["judul_forum"]);
    $isi_forum = stripslashes($data["isi_forum"]);
    $tanggal_forum = date('Y-m-d');
    $id_pengguna = stripslashes($data["id_pengguna"]);
    $kategori_forum = $data["kategori"];
    $tambah_forum = "INSERT INTO forum VALUES(
                    '',
                    '$judul_forum',
                    '$isi_forum',
                    '$kategori_forum',
                    '$image',
                    '$tanggal_forum',
                    '$id_pengguna'
                    )";
    mysqli_query($conn, $tambah_forum) or die("Connection failed: " .mysqli_connect_error());
    $last_id = mysqli_insert_id($conn);
    return $last_id;
}

function tambahKomentar($data) {
    global $conn;

    $id_forum = $data["id_forum"];
    $id_pengguna = $data["id_pengguna"];
    $tanggal_komentar = date('Y-m-d');
    $isi_komentar = stripslashes($data["isi_komentar"]);

    $tambah_komentar = "INSERT INTO komentar VALUES(
                        '',
                        '$id_forum',
                        '$id_pengguna',
                        '$isi_komentar',
                        '$tanggal_komentar')";
    mysqli_query($conn, $tambah_komentar);
    return mysqli_affected_rows($conn);
}

function buatInformasi($data) {
    global $conn;

    if (!empty($_FILES['image']['tmp_name'])) {
        $file = $_FILES['image']['tmp_name'];
        if (!isset($file) ){
            echo "<script>alert('Pilih file gambar');</script>";
            return false;
        }

        else {
            $image = addslashes(file_get_contents($_FILES['image']['tmp_name']));
            $image_name = addslashes($_FILES['image']['name']);
            $image_size = getimagesize($_FILES['image']['tmp_name']);
            if ($image_size == false) {
                echo "<script>alert('File yang dipilih bukan gambar');</script>";
                return false;
            }
        }
    } else {
        $image = NULL;
    }

    $id_pengguna = $data["id_pengguna"];
    $judul_info = stripslashes($data["judul_info"]);
    $isi_info = stripslashes($data["isi_info"]);
    $tanggal_info = date('Y-m-d');
    $status = "moderasi";

    $tambah_info = "INSERT INTO informasi VALUES(
                    '',
                    '$judul_info',
                    '$isi_info',
                    '$tanggal_info',
                    '$image',
                    '$status',
                    '$id_pengguna')";
    mysqli_query($conn, $tambah_info) or die(mysqli_error());
    return mysqli_affected_rows($conn);
}

function gantiPass($data) {
    global $conn;
    $id_pengguna = $data["id_pengguna"];
    $password = $data["passwordLama"];
    $passBaru = $data["passwordBaru"];
    $konfrimPass = $data["konfirmPass"];

    $result = mysqli_query($conn, "SELECT * FROM pengguna WHERE id_pengguna = $id_pengguna") or die(mysqli_error());

    // cek username
    if ( mysqli_num_rows($result) === 1 ) {
        // cek password
        $row = mysqli_fetch_assoc($result);
        if ( password_verify($password, $row["password"]) ) {
           
            // cek konfirmasi password
            if ($passBaru !== $konfrimPass) {
                return 0;
            }

            // enkripsi password
            $passBaru = password_hash($passBaru, PASSWORD_DEFAULT);

            mysqli_query($conn, "UPDATE pengguna SET
                password = '$passBaru'
            WHERE id_pengguna = $id_pengguna");
            return 1;
        }

        else {
            return 99;
        }
    }
}

function tanggal_indo($tanggal) {
    $bulan = array (
        1 => 'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember'
    );

    $tgl_indo = explode('-', $tanggal);

    return $tgl_indo[2] . ' ' . $bulan[(int)$tgl_indo[1]] . ' ' . $tgl_indo[0];
}
function hapusFoto($data) {
    global $conn;

    $id_pengguna = $data["id_pengguna"];
    mysqli_query($conn, "UPDATE pengguna SET gambar=NULL WHERE id_pengguna = $id_pengguna");
    return mysqli_affected_rows($conn);
}

function setujuInfo($data) {
    global $conn;
    $id_informasi = $data["id"];
    mysqli_query($conn, "UPDATE informasi SET status = 'disetujui' WHERE id_informasi = $id_informasi") or die(mysqli_error());
    return mysqli_affected_rows($conn);
}

function tolakInfo($data) {
    global $conn;
    $id_informasi = $data["id"];
    mysqli_query($conn, "DELETE FROM informasi WHERE id_informasi = $id_informasi") or die(mysqli_error());
    return mysqli_affected_rows($conn);
}

function hapusForum($data) {
    global $conn;
    $id_forum = $data["id"];
    mysqli_query($conn, "DELETE FROM komentar
                WHERE id_forum = $id_forum") 
                or die(mysqli_error());
    mysqli_query($conn, "DELETE FROM forum
                WHERE id_forum = $id_forum")
                or die(mysqli_error());
    return mysqli_affected_rows($conn);
}

?>
