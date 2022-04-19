# Table footer
This [Laravel Nova](https://nova.laravel.com) package is used for calculating the total of every column that supports every function you need.

![Detail View](https://banners.beyondco.de/Total%20Filed%20for%20index.png?theme=light&packageManager=composer+require&packageName=badinansoft%2Ftable-footer&pattern=architect&style=style_1&description=This+packages+use+for+total++of+every+columns+as+function+you+need+&md=1&showWatermark=1&fontSize=100px&images=calculator&widths=auto)


## Requirements

- `php: >=7.4`
- `laravel/nova: ^3.0`

### Note: May this packge don't work with nova 4

## Features
- Add a footer to any index table you want.
- Any calculatable columns you want you can get total 
- Support this function
  - sum 
  - count
  - avg
  - min
  - max

## Screenshot
![Detail View](https://media4.giphy.com/media/nWJqD2oyfEYc8nLvzF/giphy.gif?cid=790b7611652ece980a7c1499a288663403557a9967b593e9&rid=giphy.gif&ct=g)


## Installation

Install the package in a Laravel Nova project via Composer:

```bash
composer require badinansoft/table-footer
```


## Usage
To use these packages just need to install the package by the above command then for any resource any index filed add this method:-

```php
     ID::make(__('ID'), 'id')->calculate('count',__('Total Count')),
```
By above code in the footer of the ID column will show the total count of the id's 
The ``` calculate($function,$label,$symbol='')``` accept 3 argument as you see also support localization 
2 arguments are required but $symbol is not required just for adding the symbol of the currency end of the amount 

```php
     Number::make(__('Amount'),'amount')
                ->calculate('avg',__('Average Amount'),'$'),
```

