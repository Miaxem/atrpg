<?php
session_start();
include "connexion.php";

$nom = $_POST['nom'];
$genre = $_POST['genre'];
$pass = $_POST['pass'];
$stat = $_POST['stat'];

if ($nom == "" || $pass == "") {
  $probleme = 1;
}

$nom = substr(ucfirst(strtolower(strip_tags($nom))), 0, 12);
$pass = $pass . substr($nom, 0, 3) . substr($nom, -1);
$pass = md5($pass);
$stmt = $db->prepare("SELECT id FROM hrpg WHERE nom=:nom");
$stmt->execute([
  ':nom' => $nom,
]);

$row = $stmt->fetch();
$id = $row[0];

if ($id != "") {
  $probleme = 2;
}
if (isset($probleme)) { ?>
  <html>
  <head>
    <meta http-equiv="refresh" content="0;URL=new.php?text=erreur">
  </head>
  <body>
  </body>
  </html>
  <?php
}
else {
  if (empty($stat)) {
    $str = array_rand([1,2,4,5], 1);
    $mind = 6 - $str;
  }
  else {
    $str = $stat[0];
    $mind = $stat[1];
  }
  $hp = 5 + rand(0, 5);
  $stmt = $db->prepare("SELECT tag1 FROM hrpg WHERE hp > 0 AND id > 1 ORDER BY RAND()");
  $stmt->execute();
  $row = $stmt->fetch();
  $tag1 = $row[0];

  $stmt = $db->prepare("SELECT tag2 FROM hrpg WHERE hp > 0 AND id > 1 ORDER BY RAND()");
  $stmt->execute();
  $row = $stmt->fetch();
  $tag2 = $row[0];

  $stmt = $db->prepare("SELECT tag3 FROM hrpg WHERE hp > 0 AND id > 1 ORDER BY RAND()");
  $stmt->execute();
  $row = $stmt->fetch();
  $tag3 = $row[0];

  if ($tag1 == "") {
    $tag1 = " ";
  }
  if ($tag2 == "") {
    $tag2 = " ";
  }
  if ($tag3 == "") {
    $tag3 = " ";
  }

  try {
    $stmt = $db->prepare("INSERT INTO hrpg (nom,mdp,hf,str,mind,hp,tag1,tag2,tag3) VALUES(:nom,:pass,:genre,:str,:mind,:hp,:tag1,:tag2,:tag3)");
    $stmt->execute([
      ':nom' => $nom,
      ':pass' => $pass,
      ':genre' => $genre,
      ':str' => $str,
      ':mind' => $mind,
      ':hp' => $hp,
      ':tag1' => $tag1,
      ':tag2' => $tag2,
      ':tag3' => $tag3,
    ]);

    $id = $db->lastInsertId();
  } catch (Exception $e) {
    die($e->getMessage());
  }

  $_SESSION['id'] = $id;
  ?>

  <html>
    <?php include 'header.php'; ?>
    <div>
      <div><?php print $nom; ?> entre en scène.</div>
      <div>Bienvenue dans notre grande aventure.</div>
      <div><a href="main.php">C'est parti.</a></div>
    </div>
    <?php include "footer.php"; ?>
  </html>
  <?php
}
?>
