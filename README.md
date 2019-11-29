# XML generator (optimization)

Optimization of RAM memory usage in generating large XML files in php scripts.

Input data source is RDMS **Postgres 11.5** and **Mysql 5.7** (out of the box)  

Input and Output optimization:
- querying data from tables with **1M** rows via cursor (where possible)
- stream write to file using `XmlWriter` class, which flushes to output file implicitly when buffer exceeds ~4 KB

## Characteristics
- PHP 7.3.11  
- _(driver)_ mysqli driver mysqlnd 5.0.12-dev  
- _(server)_ MySql mysqld  Ver 5.7.21-1ubuntu1 for Linux on x86_64 ((Ubuntu))  
- _(driver)_ PostgreSQL(libpq) Version 10.10 (Ubuntu 10.10-0ubuntu0.18.04.1) 
- _(server)_ PostgreSQL 11.5  

## Benchmarks
- **Postgres (PDO)**  
  Connecting via  
  `$pdo = new PDO('pgsql:host=localhost;dbname=vsim;user=postgres;password=postgres')`  
  Querying via  
  `$stmt = $pdo->query($sql);`   
  Fetching via  
  `$data = $stmt->fetch(PDO::FETCH_ASSOC);`

```$xslt
Generating categories:
Query time: 1.57
Total fetched categories: 1000000
1000000 objects
Generating offers:
Query time: 11.14
Total fetched offers: 1000022
1000022 objects
Время выполнения: 102.30 Sec
Использовано ОЗУ: 2.00 MB
Total file size: 191.55 MB

```

- **Mysqli (not PDO)**   
  Connecting via  
  ```
  $instance = mysqli_init();
  $instance->real_connect('localhost', 'root', 'roots', 'test', 3306);
  ```
  Querying via  
  ```
  $result = $pdo->real_query('SELECT id, name, parent_id FROM category');
  $pdo->use_result()
  ```
  Fetching via  
  
  `$data = $result->fetch_assoc()`

```
Generating categories:
Query time: 0.05
841335 objects
Total fetch time: 5.044661283493

Generating offers:
Query time: 61.32
841322 objects
Total fetch time: 5.2643299102783

Время выполнения: 144.46 Sec
Использовано ОЗУ: 2.00 MB
Total file size: 139.18 MB
```

- **MySql (PDO)**  
  Connect via  
  `$pdo = new PDO('mysql:host=localhost;dbname=test;port=3306', 'root', 'roots')`


```
Generating categories:
Query time: 1.05
Total fetched categories: 841335
841335 objects
Generating offers:
Query time: 64.44
Total fetched offers: 841322
841322 objects
Время выполнения: 161.42 Sec
Использовано ОЗУ: 368.39 MB
Total file size: 139.18 MB

```

- **Phalcon PDO (MySql)**

  Connect via
    ```
    $pdo = new Phalcon\Db\Adapter\Pdo\Mysql([
        "host"     => "localhost",
        "dbname"   => "test",
        "port"     => 3306,
        "username" => "root",
        "password" => "roots",
    ])
    ```

```
Generating categories:
Query time: 1.25
Total fetched categories: 841335
841335 objects
Generating offers:
Query time: 68.75
Total fetched offers: 841322
841322 objects
Время выполнения: 155.11 Sec
Использовано ОЗУ: 368.39 MB
Total file size: 139.18 MB
```

## Result

Real cursors are possible if connection established via
 1. Postgres PDO 
 2. MySqli class (with MYSQLI_STORE_RESULT const)

Script takes static `2 MB` RAM and does not depend on input data size.

**MySql PDO** or **Phalcon MySql PDO** loads all data from table in memory anyway and takes `368 MB` RAM and depends on input data size.


## Appendix

SQL for creating schema and seeding data in _Postgres_:
```$xslt
create table offer
(
    id         bigserial primary key,
    available  bool         not null,
    url        varchar(255) not null default '',
    price      int          not null default 0,
    categoryId int          not null
);

insert into "offer" (available,
                     url,
                     price,
                     category_id)
select mod(i, 3)::bool,
       concat('https://url.ru/', left(md5(i::text), 15)),
       mod(floor(random() * 10 * i)::int, 100000),
       mod(i, 100)
from generate_series(1, 1000000) s(i);

create table category
(
    id        serial primary key,
    name      varchar(60) not null,
    parent_id int
);

insert into "category" (name,
                        parent_id)
select concat('cat_', left(md5(i::text), 25)),
       CASE
           WHEN mod(floor(random() * i)::int, 100) < 50
               THEN null
           ELSE mod(floor(random() * i)::int, 100)
           END
from generate_series(1, 1000000) s(i);

```

SQL for creating schema and seeding data in _Mysql_ (for creating `generate_series` function analog used [this sql](https://github.com/gabfl/mysql_generate_series)):
```
CALL generate_series(1, 1000000, 1);

create table category
(
	id int auto_increment primary key,
	name varchar(255) not null,
	parent_id int null
);

INSERT INTO category(name, parent_id)
    select lpad(conv(floor(rand() * pow(36, 6)), 10, 36), 6, 0), floor(rand() * 100)
        from series_tmp;

create table offer
(
	id int auto_increment primary key,
	available smallint not null,
	price int not null,
	url varchar(255) default '' not null,
	category_id int not null
);


INSERT INTO offer(available, 
                  url, 
                  price, 
                  category_id)
select 1,
       concat('http://url.com/', lpad(conv(floor(rand() * pow(36, 6)), 10, 36), 6, 0)),
       floor(rand() * 100),
       floor(rand() * 100)
from series_tmp;
```
