# Patient

This is a Laravel package for creation of patient and requester tables with backend.

### Requirements

- PHP 8.1
- Laravel ^9.0

### Installation

Have to be done...

## Patient Model

##### Accesors

```php
$p->name; //Full name
$p->salutation // Dhr/Mevr + full name 
$p->address-> //building, building_nr, building_addition, postcode, city, street
```

##### Finding or creating a new Patient

```php
Patient::findOrCreate( ...patient params)
```

##### Searching patients

```php
Patient::filtered(['dob'=>'2000-11-11',...])->get()
```

##### Add Note

```php 
$p = Patient::first();
$p->addNote('Hello world');
```

##### Remove Note

```php
$p->rmNote('2021-10-10 13:41:21');
```

##### Add Action

Type: appointment, request, test, email, letter, sms, call

```php
$p->addAction(string $type,string $subject, mixed $details)
```

## Requester Model

##### Accesors

```php
$p->name; //Full name
$p->list_item // lastname, initials prefix (city) 
$p->address-> //building, building_nr, building_addition, postcode, city, street
```

##### Finding or creating a new Requester

```php
Patient::findOrCreate( ...requester params)
```

##### Searching requester

```php
Patient::filtered(['lastname'=>'gr',...])->get()
```

## Casting

##### Phone

Phone numbers will be cast to database in numbers only. `Netnummer` will be added if necessary. Getters will reformat
the phone numbers more readable.

##### Initials

Initials will be cast to database in alphabetic characters only. Getters will reformat the initials with dot.

## Testing

Database could be filled.

```php
Requester::factory(3)
->HasPatients(10)
->create();
```
