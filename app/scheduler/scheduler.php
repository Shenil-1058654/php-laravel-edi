<?php

declare(strict_types=1);

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require __DIR__ . '/../vendor/autoload.php';

use GO\Scheduler;

// Eén crontab-regel op de server: * * * * * php /var/www/html/scheduler/scheduler.php
// Dit script bepaalt zelf welke jobs op dit moment aan de beurt zijn.
$scheduler = new Scheduler();

// Voorbeeld (zelfde werkwijze als le_duc_edi): elke job draait als eigen proces,
// zodat een crash in de ene job de andere niet stillegt.
// Kies de minuut van een stap NA de minuut van de stap waarvan hij afhankelijk is.
//
// $scheduler->php(__DIR__ . '/jobs/read_x400.php')->at('1-59/10 * * * *');
// $scheduler->php(__DIR__ . '/jobs/process_x400_to_db.php')->at('2-59/10 * * * *');

$scheduler->run();
