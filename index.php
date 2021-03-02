<?php
declare(strict_types=1);
require_once 'class/OpenWeather.php';
$weather = new OpenWeather('3d04fb24f668b9a6f6f096eeffe21705');
$error = null;
try {
    // $data = explode(' '); // to trigger PHP errors
    $forecast = $weather->getForecast('Toulouse,fr');
    $today = $weather->getTodayForecast('Toulouse,fr');
} catch (CurlException $e) {
    exit($e->getMessage());
} catch (HTTPException $e) {
    $error = $e->getMessage() . ' ' . $e->getMessage();
} catch(Error $e) {
    $error = $e->getMessage();
}
require_once 'elements/header.php';
?>    

<?php if ($error) : ?>
<div class="alert alert-danger">
    <?= $error ?>
</div>
<?php else : ?>

<div class="card mb-4">
    <div class="card-body">
        <div class="card-title">
            <h2>Actuellement (<?= $today['date']->format('d/m/Y') ?>)</h2>
        </div>
        <ul>
            <li>
                <?= $today['date']->format('h') .'h' ?> : 
                <?= $today['description'] ?>, 
                <?= $today['temp'] . '°C' ?>
            </li>
        </ul>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="card-title">
            <h2>Prédictions</h2>
        </div>
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
    </div>
</div>

<?php endif ?>
<?php
require_once 'elements/footer.php';
?>