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
        //mencegah serangan injeksi skrip.
        $strBersih = htmlspecialchars($strBersih);
        //Menghapus karakter yang tidak perlu (spasi ekstra, tab, baris baru) dari data masukan pengguna (dengan fungsi PHP trim())
        $strBersih = trim($strBersih);
        //Menghapus garis miring (\) dari data masukan pengguna (dengan fungsi PHP stripslashes())
        // https://www.w3schools.com/php/php_form_validation.asp
        $strBersih = stripslashes($strBersih);
        // menghapus tag HTML dan PHP dari sebuah string.
        $strBersih = strip_tags($strBersih);
        return $strBersih;
    }

?>