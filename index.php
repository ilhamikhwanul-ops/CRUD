<style type="text/css">
    .header {
        background-color: orange;
    }
</style>

<?php

// Menghubungkan ke database
include_once("config.php");

// Mengambil seluruh data alat
$result = mysqli_query(
    $mysqli,
    "SELECT * FROM alat ORDER BY id DESC"
);

?>

<html>
<head>
    <title>SIM RS</title>
</head>

<body>

    <b>Data Alat Elektromedis</b>
    <br>

    <a href="add.php">Tambah Alat</a>
    <br><br>

    <table width="80%" border="1">

        <tr class="header">
            <th>No</th>
            <th>Nama Alat</th>
            <th>Tahun</th>
            <th>Merek</th>
            <th>Lokasi</th>
            <th>Aksi</th>
        </tr>

        <?php
        $i = 1;

        while ($user_data = mysqli_fetch_assoc($result)) {

            echo "<tr>";

            echo "<td>".$i."</td>";

            echo "<td>".htmlspecialchars($user_data['nama_alat'])."</td>";

            echo "<td>".htmlspecialchars($user_data['tahun'])."</td>";

            echo "<td>".htmlspecialchars($user_data['merek'])."</td>";

            echo "<td>".htmlspecialchars($user_data['lokasi'])."</td>";

            echo "<td>
                <a href='edit.php?id=".$user_data['id']."'>Edit</a> |
                <a href='delete.php?id=".$user_data['id']."'
                onclick=\"return confirm('Yakin ingin menghapus data?')\">
                Delete</a>
            </td>";

            echo "</tr>";

            $i++;
        }
        ?>

    </table>

</body>
</html>