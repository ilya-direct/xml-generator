<?php

require_once __DIR__ . '/vendor/autoload.php';


use Phalcon\Loader;

$loader = new Loader();

$loader->registerDirs(
    [
        'PhalconModels',
    ]
);

$loader->register();

$container = new \Phalcon\Di();
$container->set(
    'db',
    function () {
        return new \Phalcon\Db\Adapter\Pdo\Mysql(
            [
                'host'     => 'localhost',
                'username' => 'root',
                'password' => 'roots',
                'dbname'   => 'test',
            ]
        );
    }
);

$container->set(
    'modelsManager',
    new \Phalcon\Mvc\Model\Manager()
);

$container->set(
    'modelsMetadata',
    new \Phalcon\Mvc\Model\MetaData\Memory()
);


/*$categoriesArray = [
    ['id' => 1, 'name' => 'cname1', 'parent_id' => 11],
    ['id' => 2, 'name' => 'cname2', 'parent_id' => 22],
    ['id' => 3, 'name' => 'cname3'],
];
$cIterator = new ArrayCategoryIterator($categoriesArray);*/

$time = microtime(true);

/*$offersArray = [
    ['id' => 121, 'available' => true,  'url' => 'tech/121', 'price' => 11, 'category_id' => 22],
    ['id' => 144, 'available' => false, 'url' => 'tech/133', 'price' => 24, 'category_id' => 22],
];
$oIterator = new ArrayOfferIterator($offersArray);*/


$date = '29-11-2019';
$shopName = 'Technopark Shop';
$companyName = 'Technopark';
$url = 'https://technopark.ru';

//$pdo = new PDO('pgsql:host=localhost;dbname=vsim;user=postgres;password=postgres');
//$pdo = new PDO('mysql://root:roots@localhost:3306/test'); // not working with mysql 5.7
$pdo = new PDO('mysql:host=localhost;dbname=test;port=3306', 'root', 'roots');
$pdoPhalcon = new Phalcon\Db\Adapter\Pdo\Mysql([
    "host"     => "localhost",
    "dbname"   => "test",
    "port"     => 3306,
    "username" => "root",
    "password" => "roots",
]);

$mysqliFn = function ($host, $user, $password, $dbname, $port) {
    $instance = mysqli_init();
    $instance->real_connect('localhost', 'root', 'roots', 'test', 3306);

    return $instance;
};
//$mysqli = $mysqliFn('localhost', 'root', 'roots', 'test', 3306);



$cIterator = new Yml\Iterators\CategoryIteratorPhalconModelResultSet();
//$cIterator = new Yml\Iterators\CategoryIteratorPhalconPdo($pdoPhalcon);
//$cIterator = new Yml\Iterators\CategoryIteratorPhalconPdoFetchAll($pdoPhalcon);
//$cIterator = new Yml\Iterators\CategoryIteratorPdoFetch($pdo);
//$cIterator = new Yml\Iterators\CategoryIteratorMysqli($mysqli);

$oIterator = new Yml\Iterators\OfferIteratorPhalconModelResultSet();
//$oIterator = new Yml\Iterators\OfferIteratorPhalconPdo($pdoPhalcon);
//$oIterator = new Yml\Iterators\OfferIteratorPhalconPdoFetchAll($pdoPhalcon);
//$oIterator = new Yml\Iterators\OfferIteratorPdoFetch($pdo);
//$oIterator = new Yml\Iterators\OfferIteratorMysqli($mysqli);

$fileName = __DIR__  . '/tempX.xml';
(new Yml\Wrapper($fileName))
    ->generate(
        $shopName,
        $companyName,
        $url,
        $cIterator,
        $oIterator,
    );

//fclose($fileHandler);

$totalTimeMs = microtime(1) - $time;
$totalTimeStr = number_format($totalTimeMs, 2);

$bytes =  memory_get_peak_usage(true);
//$mBytes =  number_format($bytes, 2);
//$mKBytes = number_format($bytes / 1024, 2);
$mMBytes = number_format($bytes / 1024 / 1024, 2);

print("Время выполнения: {$totalTimeStr} Sec\n");
print("Использовано ОЗУ: {$mMBytes} MB\n");

$fileSize = number_format( filesize($fileName) / 1024 / 1024, 2);
print("Total file size: {$fileSize} MB\n");

// i/o time