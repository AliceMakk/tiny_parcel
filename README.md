# Tiny Parcel API

A beautiful API which provides an opportunity for a user to send their request about price delivery of their parcel to the Tiny Parcel.  
The enquire will then get estimated with the <strike>highest</strike> best quote and persisted in the DB for further accessing by its ID.

## Technologies
* <a href='https://www.postgresql.org/' target='_blank'>PostgreSQL</a> relational database
* <a href='https://symfony.com/' target='_blank'>Symfony 5</a> php framework
* <a href='https://getcomposer.org/' target='_blank'>Composer</a> dependency manager
* <a href='https://www.doctrine-project.org/' target='_blank'>Doctrine</a> ORM
* <a href='https://phpunit.de/' target='_blank'>PHPUnit</a> testing framework
* <a href='https://github.com/symfony/monolog-bundle' target='_blank'>Monolog</a> logger
* <a href='https://www.postman.com/downloads/' target='_blank'>Postman</a> testing API tool

## Installation
* <code>git clone git@github.com:AliceMakk/tiny_parcel.git</code>
* Run local instance of PostgreSQL and update credentials exposed in global DATABASE_URL in .env
* Add vendor dependencies <code>composer i</code>
* Create needed tables in the DB <code>php bin/console doctrine:migrations:migrate</code>
* Seed the DB with dummy pricing model and user <code>php bin/console doctrine:fixtures:load</code>
* Run server <code>symfony server:start</code> via <a href='https://symfony.com/download' target='_blank'>Symfony-CLI</a>


Required header for each request:
* <code>X-AUTH-TOKEN: _WvpbpJOns9ZxdOIuxWMTsFFj0AdZY0KubskvSUhIb0 </code>

## get <code>/parcels/{id}</code>

Example of response: 
<code>
{"id":396,"name":"Tiny box","weight":"0.40","declaredValue":1300,"volume":"0.0007900","quote":"39.00","price_model":"value"}
</code>

## post <code>/parcels/</code>

Example of request: 
<code>
{
 "name" : "Little phone",
 "weight" : "0.5",
 "volume" : "0.0005",
 "declared_value" : "500"
}
</code>

Example of response: 
<code>
{"status":"Parcel saved!","id":1}
</code>

## put <code>/parcels/</code>

Example of request: 
<code>
{
 "name" : "Expensive Sumsung tv",
 "weight" : "20",
 "volume" : "2",
 "declared_value" : "5000"
}
</code>

Example of response: 
<code>
{"status":"Parcel updated!","id":1}
</code>

## delete <code>/parcels/{id}</code>

Example of response: 
<code>
{"status":"Parcel deleted!"}
</code>

## get <code>/prices/?parcelIds={ids}</code>

Example of response: 
<code>
[{"parcelId":399,"quote":"$39.00"},{"parcelId":400,"quote":"$100.00"}]
</code>

