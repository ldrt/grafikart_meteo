<?php
require_once 'class/OpenWeather.php';
$weather = new OpenWeather('3d04fb24f668b9a6f6f096eeffe21705');
$forecast = $weather->getForecast('Toulouse,fr');
$today = $weather->getTodayForecast('Toulouse,fr');
?>    

<h2>Actuellement (<?= $today['date']->format('d/m/Y') ?>)</h2>
<ul>
    <li>
        <?= $today['date']->format('h') .'h' ?> : 
        <?= $today['description'] ?>, 
        <?= $today['temp'] . '°C' ?>
    </li>
</ul>

<h2>Prédictions</h2>
<ul>
    <?php foreach ($forecast as $k => $day) : ?>
    <h3><?= $k ?></h3>
    <?php foreach ($day as $d) : ?>
    <li>
        <?= $d['date']->format('h') .'h' ?> : 
        <?= $d['description'] ?>, 
        <?= $d['temp'] . '°C' ?>
    </li>
    <?php endforeach ?>
    <?php endforeach ?>
</ul>