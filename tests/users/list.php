<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Services\FirestoreService;

$service = new FirestoreService();
$service->collection('users');

$documents = $service->getDocuments();

foreach ($documents as $document) {
    if ($document->exists()) {
        printf("Documento de id: {$document->id()}");
        print_r($document->data());
        printf(PHP_EOL);
    }
}
