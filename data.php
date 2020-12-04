<?php
header('Content-Type: application/json');
require './server.php';
/* mysqli_set_charset($conn, 'utf8mb4'): json_encode nécessite que les données reçu soient encodées en UTF-8,
sinon lorsqu'elles contiennent des caractères spéciaux comme les accents
aucun résultat n'est retourné
*/
mysqli_set_charset($conn, 'utf8mb4'); 
$result = mysqli_query($conn,
 "select t.id_tweet,u.id_user,u.age,u.pays, t.hashtag, t.texte from user u join tweet t on u.id_user = t.id_user");
 
$data = array();
while ($row = mysqli_fetch_object($result))
{
    array_push($data, $row);
}
echo json_encode($data);
exit();
?>