<?php

declare(strict_types=1);
require_once 'vendor/autoload.php';

use Platine\DocxTemplate\Archive\NullExtractor;
use Platine\DocxTemplate\Convertor\NullConvertor;
use Platine\DocxTemplate\DocxTemplate;
use Platine\DocxTemplate\Renderer\PlatineTemplateRenderer;
use Platine\Filesystem\Adapter\Local\LocalAdapter;
use Platine\Filesystem\Filesystem;
use Platine\Template\Template;

//////// TODO: use dependency injection to get instances
$localAdapter = new LocalAdapter(); 
$filesystem = new Filesystem($localAdapter);
$template = new Template();
$renderer = new PlatineTemplateRenderer($template); //new NullRenderer();
// $convertor = new LibreOfficePDFConvertor();
$convertor = new NullConvertor();
$extractor = new NullExtractor();
// $extractor = new Zip();

//Use container to resolve automatically 
//all the given parameters
$l = new DocxTemplate(
    $filesystem,
    $renderer,
    $convertor,
    $extractor
); 

$l->setTemplateFile(dirname(__FILE__) . '/invoice.docx')
   ->setData([
       'company' => [
           'name' => 'Galaxy Finance',
           'address' => 'PK 15 Road of Boali',
           'phone' => '+236-72000000',
       ],
       'customer' => [
         'name' => 'Banabool Kitoko',
         'phone' => '+236-75111111',
         'email' => 'bkitoko@example.com',
       ],
       'invoice' => [
           'no' => '2021090500033',
           'date' => '2021-09-05 11:25:08',
           'sub_total' => '3,450 FCFA',
           'tax' => '19 %',
           'total_amount' => '4,105 FCFA',
           'due_days' => 15,
       ],
       'items' => [
           [
               'no' => 1,
               'name' => 'Savon',
               'price' => 250,
               'quantity' => 2,
               'total' => 500,
           ],
           [
               'no' => 2,
               'name' => 'Sucre',
               'price' => 500,
               'quantity' => 5,
               'total' => 2500,
           ],
           [
               'no' => 3,
               'name' => 'Omo',
               'price' => 150,
               'quantity' => 3,
               'total' => 450,
           ]
       ],
   ]);
$l->process();
$l->convert();

echo 'Template output file path: ' . $l->getOutputTemplateFile() . "\n";
echo 'Template conversion file path: ' . $l->getConversionFile() . "\n";
