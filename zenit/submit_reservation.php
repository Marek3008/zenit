<?php
// Pripojenie k databáze
$host = 'localhost';
$dbname = 'zenitkk40';
$username = 'root';
$password = '';

try {
  // Vytvorenie pripojenia k databáze
  $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // Inicializácia premenných a chybové správy
  $name = $email = $phone = $location_id = $ski_pass_type = "";
  $number_of_days = $date = $number_of_people = 0;
  $errors = [];

  // Kontrola, či bol formulár odoslaný
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
      // Validácia mien
      if (isset($_POST["name"]) && !empty(trim($_POST["name"])) && preg_match("/^[a-zA-Z\s]{4,64}$/", trim($_POST["name"]))) {
          $name = trim($_POST["name"]);
      } else {
          $errors[] = "Celé meno musí obsahovať minimálne 4 znaky a maximálne 64 znakov (len písmená a medzery).";
      }

      // Validácia emailu
      if (isset($_POST["email"]) && !empty(trim($_POST["email"])) && filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)) {
          $email = trim($_POST["email"]);
      } else {
          $errors[] = "Zadajte platný email.";
      }

      // Validácia telefónu
      if (isset($_POST["phone"]) && !empty(trim($_POST["phone"])) && preg_match("/^[0-9 +()]*$/", trim($_POST["phone"]))) {
          $phone = trim($_POST["phone"]);
      } else {
          $errors[] = "Zadajte platné telefónne číslo.";
      }

      // Kontrola lokality
      if (isset($_POST["location"]) && !empty(trim($_POST["location"]))) {
          $location_id = trim($_POST["location"]);
      } else {
          $errors[] = "Vyberte lokalitu.";
      }

      // Validácia typu skipasu
      if (isset($_POST["ski_pass_type"]) && !empty(trim($_POST["ski_pass_type"]))) {
          $ski_pass_type = trim($_POST["ski_pass_type"]);
      } else {
          $errors[] = "Vyberte typ skipasu.";
      }

      // Validácia počtu dní
      if (isset($_POST["number_of_days"])) {
          $number_of_days = intval($_POST["number_of_days"]);
          if ($ski_pass_type == 'multi_day' && $number_of_days < 1) {
              $errors[] = "Počet dní musí byť aspoň 1 pre viacdňový skipas.";
          } elseif ($ski_pass_type == 'season') {
              $number_of_days = 0; // Pre sezónny skipas sa nastaví počet dní na 0
          }
      }

      // Validácia dátumu
      if (isset($_POST["date"]) && !empty(trim($_POST["date"])) && strtotime(trim($_POST["date"])) >= strtotime("today")) {
          $date = trim($_POST["date"]);
      } else {
          $errors[] = "Vyberte platný termín (dátum).";
      }

      // Validácia počtu osôb
      if (isset($_POST["number_of_people"])) {
          $number_of_people = intval($_POST["number_of_people"]);
          if ($number_of_people < 1) {
              $errors[] = "Počet osôb musí byť aspoň 1.";
          }
      }

      // Ak sú údaje platné, uložte ich do databázy
      if (empty($errors)) {
          // Vytvorenie záznamu v tabuľke reservations
          $stmt = $pdo->prepare("INSERT INTO reservations (location_id, mail, telefon, meno_priezvisko, pocet_osob, typ_skipasu, pocet_dni, termin, celkova_suma, cas) VALUES (:location_id, :email, :phone, :name, :number_of_people, :ski_pass_type, :number_of_days, :date, :total_price, NOW())");
          
          // Predpokladáme základnú cenu na základe lokality, pridať logiku na výpočet ceny
          $base_price = 100; // Tu by ste mali načítať základnú cenu z databázy na základe `location_id`
          $total_price = $base_price; // Začneme s touto základnou cenou

          switch ($ski_pass_type) {
              case 'child':
                  $total_price *= 0.7; // 70% základnej ceny
                  break;
              case 'junior':
                  $total_price *= 0.8; // 80% základnej ceny
                  break;
              case 'adult':
                  break; // 100% základnej ceny
              case 'senior':
                  $total_price *= 0.85; // 85% základnej ceny
                  break;
          }

          $stmt->execute([
              'location_id' => $location_id,
              'email' => $email,
              'phone' => $phone,
              'name' => $name,
              'number_of_people' => $number_of_people,
              'ski_pass_type' => $ski_pass_type,
              'number_of_days' => $number_of_days,
              'date' => $date,
              'total_price' => $total_price
          ]);

          // Redirect alebo úspešná správa
          header("Location: index.php");

      } else {
          // Zobraziť chybové správy
          foreach ($errors as $error) {
              echo "<p style='color:red;'>$error</p>";
          }
          
      }
  }
} catch (PDOException $e) {
  echo "Chyba pripojenia: " . $e->getMessage();
}
?>