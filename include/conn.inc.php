<?php 
$hostname = "localhost";
$username = "root";
$password = "";
$databasename = "olams";

$conn = mysqli_connect("$hostname", "$username", "$password", "$databasename");

if(mysqli_connect_errno()){
    echo "Koneksi ke database gagal", mysqli_connect_error();
}
// else{
//     echo "koneksi ke database berhasil";
// }
function cleanValue($strhtml)
    {
        $strBersih = $strhtml;
        $strBersih = htmlspecialchars($strBersih);
        $strBersih = trim($strBersih);
        $strBersih = stripslashes($strBersih);
        return $strBersih;
    }

?>