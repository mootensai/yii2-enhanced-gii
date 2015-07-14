# yii2-enhanced-gii
Yii2 Gii (generator) with Relation

[![Latest Stable Version](https://poser.pugx.org/mootensai/yii2-enhanced-gii/v/stable)](https://packagist.org/packages/mootensai/yii2-enhanced-gii)
[![License](https://poser.pugx.org/mootensai/yii2-enhanced-gii/license)](https://packagist.org/packages/mootensai/yii2-enhanced-gii)
[![Total Downloads](https://img.shields.io/packagist/dt/mootensai/yii2-enhanced-gii.svg?style=flat-square)](https://packagist.org/packages/mootensai/yii2-enhanced-gii)
[![Monthly Downloads](https://poser.pugx.org/mootensai/yii2-enhanced-gii/d/monthly)](https://packagist.org/packages/mootensai/yii2-enhanced-gii)
[![Daily Downloads](https://poser.pugx.org/mootensai/yii2-enhanced-gii/d/daily)](https://packagist.org/packages/mootensai/yii2-enhanced-gii)
[![Join the chat at https://gitter.im/mootensai/yii2-enhanced-gii](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/mootensai/yii2-enhanced-gii?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```bash
$ composer require 'mootensai/yii2-enhanced-gii:dev-master'
```

or add

```
"mootensai/yii2-enhanced-gii": "*"
```

to the `require` section of your `composer.json` file.

Then you must add this code at your config\main.php.
```php
'modules' => [
... //your another module
      'gridview' => [
          'class' => '\kartik\grid\Module',
      ],
... // your another module
    ],
```

## Usage :
Go to your gii tools, and notice the new IO Generator for models & CRUD


#Features
## Model :
1. Generate optimistic lock
2. Generate Timestamp Behaviors
3. Generate Blameable Behavior
4. Generate UUID Behavior

## CRUD :
1. Generate all CRUD with wildcard (*) of table
2. Generate related input output
3. Specify your name/label attribute for foreign keys
4. Set your column to hidden
5. Specify your skipped columns
6. Specify your skipped relations
7. Set pluralize or not
8. PDF Printable view


#To Do
1. Nested set detector & generator
2. One-page-CRUD
3. Expandable / collapsible row at index grid view for related data
4. Generate migrations for tables (like  https://github.com/mdmsoft/yii2-gii)
5. Tabbed relations view

I'm open for any improvement

# Thanks To
1. Jiwanndaru (jiwanndaru@gmail.com) for creating the tradition
2. kartik-v (https://github.com/kartik-v) for most of widgets
3. schmunk42 (https://github.com/schmunk42) for bootstrap & model base & extension
4. mdmunir (https://github.com/mdmunir) for JsBlock
