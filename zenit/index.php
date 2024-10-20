<?php
$host = 'localhost';
$dbname = 'zenitkk40';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Inicializácia premenných a chybové správy
    $name = $email = "";
    $name_err = $email_err = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Validácia mena
        if (empty(trim($_POST["name"])) || !preg_match("/^[a-zA-Z\s]{4,64}$/", trim($_POST["name"]))) {
            $name_err = "Meno musí obsahovať minimálne 4 znaky a maximálne 64 znakov (len písmená a medzery).";
        } else {
            $name = trim($_POST["name"]);
        }

        // Validácia emailu
        if (empty(trim($_POST["email"]))) {
            $email_err = "Email je povinný.";
        } elseif (!filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)) {
            $email_err = "Zadajte platný email.";
        } else {
            $email = trim($_POST["email"]);

            // Kontrola jedinečnosti emailu v DB
            $stmt = $pdo->prepare("SELECT id FROM newsletter WHERE mail = :email");
            $stmt->execute(['email' => $email]);
            if ($stmt->rowCount() > 0) {
                $email_err = "Tento email je už zaregistrovaný.";
            }
        }

        // Ak nie sú žiadne chyby, vložte údaje do DB
        if (empty($name_err) && empty($email_err)) {
            $stmt = $pdo->prepare("INSERT INTO newsletter (meno_priezvisko, mail) VALUES (:name, :email)");
            $stmt->execute(['name' => $name, 'email' => $email]);
            $name = "";
            $email = "";

        }
    }
} catch (PDOException $e) {
    echo "Chyba pripojenia: " . $e->getMessage();
}
?>


<?php
// Pripojenie k databáze
$host = 'localhost';
$dbname = 'zenitkk40';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Inicializácia premenných a chybové správy
    $name = $email = $phone = $location_id = $ski_pass_type = "";
    $number_of_days = $date = $number_of_people = 0;
    $errors = [];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Validácia mien
        if (empty(trim($_POST["name"])) || !preg_match("/^[a-zA-Z\s]{4,64}$/", trim($_POST["name"]))) {
            $errors[] = "Celé meno musí obsahovať minimálne 4 znaky a maximálne 64 znakov (len písmená a medzery).";
        } else {
            $name = trim($_POST["name"]);
        }

        // Validácia emailu
        if (empty(trim($_POST["email"])) || !filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Zadajte platný email.";
        } else {
            $email = trim($_POST["email"]);
        }

        // Validácia telefónu
        if (empty(trim($_POST["phone"])) || !preg_match("/^[0-9 +()]*$/", trim($_POST["phone"]))) {
            $errors[] = "Zadajte platné telefónne číslo.";
        } else {
            $phone = trim($_POST["phone"]);
        }

        // Kontrola lokality
        if (empty(trim($_POST["location"]))) {
            $errors[] = "Vyberte lokalitu.";
        } else {
            $location_id = trim($_POST["location"]);
        }

        // Validácia typu skipasu
        if (empty(trim($_POST["ski_pass_type"]))) {
            $errors[] = "Vyberte typ skipasu.";
        } else {
            $ski_pass_type = trim($_POST["ski_pass_type"]);
        }

        // Validácia počtu dní
        $number_of_days = intval($_POST["number_of_days"]);
        if ($ski_pass_type == 'multi_day' && $number_of_days < 1) {
            $errors[] = "Počet dní musí byť aspoň 1 pre viacdňový skipas.";
        } elseif ($ski_pass_type == 'season') {
            $number_of_days = 0; // Pre sezónny skipas sa nastaví počet dní na 0
        }

        // Validácia dátumu
        $date = trim($_POST["date"]);
        if (empty($date) || strtotime($date) < strtotime("today")) {
            $errors[] = "Vyberte platný termín (dátum).";
        }

        // Validácia počtu osôb
        $number_of_people = intval($_POST["number_of_people"]);
        if ($number_of_people < 1) {
            $errors[] = "Počet osôb musí byť aspoň 1.";
        }

        // Ak sú údaje platné, uložte ich do databázy
        if (empty($errors)) {
            // Vytvorenie záznamu v tabuľke reservation
            $stmt = $pdo->prepare("INSERT INTO reservation (location_id, mail, telefon, meno_priezvisko, pocet_osob, typ_skipasu, pocet_dni, termin, celkova_suma, cas) VALUES (:location_id, :email, :phone, :name, :number_of_people, :ski_pass_type, :number_of_days, :date, :total_price, NOW())");
            
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
            echo "Rezervácia úspešná!";
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



<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Patua+One&family=Roboto:wght@400&display=swap" rel="stylesheet">
    <title>ZenSki</title>
</head>
<body>
<div class="vsetko">
<header>
    <div class="hero-section">
        <div class="header-left">
            <h1>ZenSki</h1>
            <p>Doprajte si zimnú dovolenku na nezabudnutie. Lyžiarske stredisko ZenSki je ideálnym miestom pre lyžiarov, snowboardistov, rodiny s deťmi a milovníkov prírody.</p>
        </div>
        <div class="header-right">
            <div class="subscribe-form">
                <h2>Chcem dostávať novinky</h2>
                <form action="#" method="post">
    <input type="text" name="name" placeholder="Zadajte svoje meno" required value="<?php echo htmlspecialchars($name); ?>">
    <span style="color:red;"><?php echo $name_err; ?></span>
    
    <input type="email" name="email" placeholder="Zadajte emailovú adresu" required value="<?php echo htmlspecialchars($email); ?>">
    <span style="color:red;"><?php echo $email_err; ?></span>
    
    <input type="submit" value="Odoslať">
</form>
            </div>
        </div>
    </div>
</header>

    <section class="features">
        <div class="feature">
            <h1>01.</h1>
            <h2> Zjazdovky<br> pre všetkých</h2>
            <br>
            <p>Ponúkame širokú škálu zjazdoviek pre všetky úrovne zručností, vrátane náročných zjazdoviek pre pokročilých, ako aj miernych zjazdoviek pre začiatočníkov a rodiny s deťmi.</p>
        </div>
        <div class="feature">
            <h1>02.</h1>
            <h2>Prekrásna<br> príroda</h2>
            <br>
            <p>Stredisko sa nachádza v krásnej horskej oblasti a ponúka nádherné výhľady na okolitú prírodu. Okrem lyžovania a snowboardingu tu môžete využiť aj množstvo ďalších aktivít.</p>
        </div>
        <div class="feature">
            <h1>03.</h1>
            <h2>Lyžiarska<br>škola</h2>
            <br>
            <p>Lyžiarska škola je skvelou možnosťou pre začiatočníkov, ktorí sa chcú naučiť lyžovať alebo snowboardovať. Lyžiarskí inštruktori poskytujú individuálne alebo skupinové lekcie.</p>
        </div>
        </section>
        <div id="reservationModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Rezervácia skipasu</h2>
        <form id="reservationForm" action="submit_reservation.php" method="post">
            <label for="name">Celé meno:</label>
            <input type="text" name="name" id="name" placeholder="Zadajte celé meno" required pattern="[a-zA-Z\s]{4,64}" title="Min. 4 znaky, maximálne 64 znakov, len písmená a medzery."><br>

            <label for="email">Kontaktný email:</label>
            <input type="email" name="email" id="email" placeholder="Zadajte emailovú adresu" required><br>

            <label for="phone">Kontaktné telefónne číslo:</label>
            <input type="tel" name="phone" id="phone" placeholder="Zadajte telefónne číslo" required pattern="[0-9 +()]*" title="Len čísla, +, ( ), a medzery."><br>

            <label for="location">Výber lokality:</label>
            <select name="location" id="location" required>
                <option value="">Vyberte lokalitu</option>
                <?php
                // Načítanie lokalít z databázy
                try {
                    $stmt = $pdo->query("SELECT id, nazov FROM location ORDER BY nazov");
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<option value='{$row['id']}'>{$row['nazov']}</option>";
                    }
                } catch (PDOException $e) {
                    echo "Chyba pri načítaní lokalít: " . $e->getMessage();
                }
                ?>
            </select><br>

            <label for="ski_pass_type">Typ skipasu:</label>
            <select name="ski_pass_type" id="ski_pass_type" required>
                <option value="1_day">Jednodňový</option>
                <option value="multi_day">Viacdňový</option>
                <option value="season">Sezónny</option>
            </select><br>

            <label for="number_of_days">Počet dní:</label>
            <input type="number" name="number_of_days" id="number_of_days" min="1" value="1" required><br>

            <label for="date">Termín:</label>
            <input type="date" name="date" id="date" required min="<?php echo date('Y-m-d'); ?>"><br>

            <label for="number_of_people">Počet osôb:</label>
            <input type="number" name="number_of_people" id="number_of_people" min="1" value="1" required><br>

            <p id="total_price">Celková suma: <span id="price_amount">0</span> €</p><br>
            <input type="submit" value="Rezervovať" id="submit_btn">
        </form>
    </div>
</div>

    

    <section class="special-offers">
    <div class="offer1">
        <h3>Špeciálna <br>ponuka<br><div class="nizsie">UŠETRITE 25%</div></h3>
        <button onclick="openModal()">Rezervovať skipas</button>
    </div>
    <div class="offer2">
        <h3>Festival <br><div class="nizsie">UŽ ZA 3 DNI</h3>
        <button onclick="openModal()">Rezervovať skipas</button>
    </div>
    <div class="offer3">
        <h3>Rodinná dovolenka<br><div class="nizsie"> ŠPECIÁLNE PONUKY</div></h3>
        <button onclick="openModal()">Rezervovať skipas</button>
    </div>
</section>

<div id="reservationModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Rezervácia</h2>
        <form id="reservationForm">
            <label for="name">Meno:</label>
            <input type="text" id="name" name="name" required>
            
            <label for="phone">Telefón:</label>
            <input type="tel" id="phone" name="phone" required>
            
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="location">Lokalita:</label>
            <select id="location" name="location">
                <option value="strbske_pleso">Štrbské Pleso</option>
                <option value="solisko">Veľké Solisko</option>
                <option value="kriváň">Kriváň</option>
                <option value="končistá">Končistá</option>
            </select>

            <label for="skipas">Typ skipasu:</label>
            <select id="skipas" name="skipas">
                <option value="1_day">Jednodňový</option>
                <option value="multi_day">Viacdňový</option>
                <option value="season">Sezónny</option>
            </select>

            <label for="date">Termín:</label>
            <input type="date" id="date" name="date" required>

            <label for="persons">Počet osôb:</label>
            <input type="number" id="persons" name="persons" min="1" max="10" required>

            <button type="submit">Rezervovať</button>
        </form>
    </div>
</div>
    </section>

    <section class="locations">
    <?php 
    $stmt = $pdo->query('SELECT nazov, cena, obrazok FROM location ORDER BY cena ASC, nazov ASC');
    $locations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($locations as $location): ?>
        <div class="location" id="<?php echo strtolower(str_replace(' ', '_', $location['nazov'])); ?>">
            <img src="<?php echo $location['obrazok']; ?>" alt="<?php echo $location['nazov']; ?>">
            <h3><?php echo $location['nazov']; ?></h3>
        </div>
    <?php endforeach; ?>
</section>
    <section class="apres-ski">
    <h2>Apré ski<br>stvorené priamo pre vás</h2>
    <div class="advantages">
        <div class="advantage">
            <img src="images/icon01.svg" alt="Reštaurácie">
            <h3>Reštaurácie</h3>
            <br>
            <p>U nás nájdete širokú škálu reštaurácií a barov, ktoré ponúkajú jedlá a nápoje pre všetky chute. Dostupné v blízkosti zjazdoviek a vlekov.</p>
        </div>
        <div class="advantage">
            <img src="images/icon02.svg" alt="Nočný život">
            <h3>Nočný život</h3>
            <br>
            <p>Nočné lyžovanie, posedenie v bare, diskotéky alebo koncerty. Zúčastnite sa festivalu zábavy, ktorý začína so západom slnka.</p>
        </div>
        <div class="advantage">
            <img src="images/icon03.svg" alt="Wellness">
            <h3>Wellness</h3>
            <br>
            <p>Naše wellness centrum ponúka širokú škálu služieb, ktoré vám pomôžu uvoľniť sa a obnoviť si energiu po náročnom dni na svahu.</p>
        </div>
        <div class="advantage">
            <img src="images/icon04.svg" alt="Obchody">
            <h3>Obchody</h3>
            <br>
            <p>Ponúkame širokú škálu obchodov a služieb, ako sú lyžiarske a snowboardové predajne, požičovne, športové potreby a ďalšie.</p>
        </div>
        <div class="advantage">
            <img src="images/icon05.svg" alt="Ubytovanie">
            <h3>Ubytovanie</h3>
            <br>
            <p>Pripravili sme pre vás sieť hotelov, apartmánov a dokonca aj chatiek. Na svoje si prídu aj tí náročnejší.</p>
        </div>
        <div class="advantage">
            <img src="images/icon06.svg" alt="Kurzy">
            <h3>Kurzy</h3>
            <br>
            <p>Kurzy sú určené pre malých aj veľkých, pre začiatočníkov a pokročilých. Naučíme vás lyžovať aj padať.</p>
        </div>
    </div>
</section>

    <footer>
    <div class="footer-info">
        <img src="images/foot01.jpg" alt="foot01" class="footer-image">
        <div class="contact-info">
            <p>Základné informácia</p>
            <p>+(421) 999 000 001</p>
            <br>
            <p>Rezervácie</p>
            <p>+(421) 999 000 002</p>
            <br>
            <p>Centrum pomoci</p>
            <p>+(421) 999 000 004</p>
        </div>
        <img src="images/foot02.jpg" alt="foot02" class="footer-image">
    </div>
    <div class="footer">
        <p>ZENIT © Všetky práva vyhradené 2023, vytvorené Matúš Budoš, Stredná priemyselná škola IT.</p>
    </div>
</footer>
</div>
</body>
<script src="script.js"></script>
</html>
