<?php
$weatherData = null;
$error = null;

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST["city"])) {
    $city = urlencode($_POST["city"]);
    $apiKey = "51f6eb956f95d6d4ad706522c7bcf9f8";  
    $apiUrl = "https://api.openweathermap.org/data/2.5/forecast?q=$city&appid=$apiKey&units=metric&lang=fr";

    $response = @file_get_contents($apiUrl);
    if ($response) {
        $weatherData = json_decode($response, true);
    } else {
        $error = "Ville non trouvée ou échec de la requête API.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Météo</title>
    <style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: url('https://images.unsplash.com/photo-1508921912186-1d1a45ebb3c1?auto=format&fit=crop&w=1920&q=80') no-repeat center center fixed;
        background-size: cover;
        margin: 0;
        padding: 40px;
        text-align: center;
        color: #fff;
    }

    .overlay-dark {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1;
    }

    h2 {
        font-size: 32px;
        color: #fff;
        margin-bottom: 20px;
        position: relative;
        z-index: 2;
    }

    form {
        margin-bottom: 30px;
        position: relative;
        z-index: 2;
    }

    input[type="text"] {
        padding: 12px 16px;
        width: 250px;
        font-size: 16px;
        border: 1px solid #ccc;
        border-radius: 8px;
        outline: none;
    }

    button {
        padding: 12px 20px;
        margin-left: 10px;
        font-size: 16px;
        background-color: #0277bd;
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    button:hover {
        background-color: #01579b;
    }

    .cardContainer {
        display: flex;
        flex-direction: row;
        overflow-x: auto;
        padding: 10px;
        gap: 10px;
        scroll-snap-type: x mandatory;
        margin-top: 20px;
        position: relative;
        z-index: 2;
    }

    .card {
        background: rgba(255, 255, 255, 0.95);
        padding: 20px;
        width: 180px;
        border-radius: 20px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        flex: 0 0 auto;
        scroll-snap-align: start;
        transition: transform 0.3s ease;
        color: #333;
    }

    .card:hover {
        transform: translateY(-5px);
    }

    .weather {
        font-size: 14px;
        margin: 8px 0;
        color: #555;
        text-transform: uppercase;
    }

    .temp {
        font-size: 28px;
        color: #0277bd;
        margin: 6px 0;
        font-weight: bold;
    }

    p.time {
        font-size: 14px;
        color: #555;
        margin: 4px 0;
    }

    .info {
        font-size: 13px;
        color: #444;
        margin: 3px 0;
    }

    .city {
        color: #fff;
        font-size: 28px;
        margin-top: 0;
        position: relative;
        z-index: 2;
    }
    </style>
</head>
<body>

<div class="overlay-dark"></div>

<h2>🌤️Recherche météo</h2>
<form method="post">
    <input type="text" name="city" placeholder="Entrez le nom de la ville" required />
    <button type="submit">Rechercher</button>
</form>

<?php if ($error): ?>
    <p style="color: red; position: relative; z-index: 2;"><?= htmlspecialchars($error) ?></p>
<?php elseif ($weatherData): ?>

    <p class="city">
        <?= htmlspecialchars($weatherData['city']['name']) ?>, <?= htmlspecialchars($weatherData['city']['country']) ?>
    </p>

    <?php
    setlocale(LC_TIME, 'fr_FR.UTF-8');
    $groupedByDate = [];
    foreach ($weatherData['list'] as $entry) {
        $date = date('Y-m-d', strtotime($entry['dt_txt']));
        $groupedByDate[$date][] = $entry;
    }
    ?>
    <?php foreach ($groupedByDate as $date => $entries): ?>
        <h4 style="color: #fff; margin-top: 30px; position:relative; z-index:2;">
          <?= strftime('%A %d %B %Y', strtotime($date)) ?>   
        </h4>
        <div class="cardContainer">
            <?php foreach ($entries as $item): ?>
                <div class="card">
                    <p class="time"><?= date('H:i', strtotime($item['dt_txt'])) ?></p>
                    <p class="weather"><?= strtoupper($item['weather'][0]['description']) ?></p>    
                    <img src="https://openweathermap.org/img/wn/<?= $item['weather'][0]['icon'] ?>@2x.png" alt="icone" width="60" height="60" />
                    <p class="temp"><?= round($item['main']['temp']) ?>°C</p>
                    <p class="info">💨Vent : <?= $item['wind']['speed'] ?> m/s</p>
                    <p class="info">💧Humidité : <?= $item['main']['humidity'] ?>%</p>
                    <p class="info">📏Pression : <?= $item['main']['pressure'] ?> hPa</p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>

<?php endif; ?>

</body>
</html>
