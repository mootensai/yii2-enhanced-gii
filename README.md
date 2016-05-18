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
$ composer require mootensai/yii2-enhanced-gii:@dev
```

or add

```
"mootensai/yii2-enhanced-gii": "@dev"
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
9. Expandable / collapsible row at index grid view for related data

## Migration Generator :
1. Generate migration from your database structure (based on : https://github.com/deesoft/yii2-gii)

# To Do
1. Nested set detector & generator -> cancelled, move to -> https://github.com/mootensai/yii2-enhanced-gii-nested (unfinished)
2. One-page-CRUD template
3. ~~Generate migrations for tables (like  https://github.com/mdmsoft/yii2-gii)~~
4. RESTful template

I'm open for any improvement


# Screenshot

## Model Generator

![new enhanced gii - model](https://cloud.githubusercontent.com/assets/5844149/13099130/db81fc46-d561-11e5-85ca-a9f3c38e68d8.PNG)

## CRUD Generator

![new enhanced gii - crud](https://cloud.githubusercontent.com/assets/5844149/13099135/ebd8537e-d561-11e5-8d2e-c303e2e63bc3.PNG)

## Index 
![new enhanced gii - index new](https://cloud.githubusercontent.com/assets/5844149/13103300/df47cdbc-d587-11e5-8435-21b47759cbd8.PNG)

## View
![new enhanced gii - view](https://cloud.githubusercontent.com/assets/5844149/13099144/035c0d92-d562-11e5-940f-b36c6b051f92.PNG)

## Form
![new enhanced gii - update](https://cloud.githubusercontent.com/assets/5844149/13099149/0bf2ca40-d562-11e5-8ea5-51711be9ed48.PNG)

# Migration Generator
![migration form](https://cloud.githubusercontent.com/assets/5844149/15350030/08ab4d58-1d01-11e6-87b7-4dd621a5bef6.JPG)


# Thanks To
1. Jiwanndaru (jiwanndaru@gmail.com) for creating the tradition
2. kartik-v (https://github.com/kartik-v) for most of widgets
3. schmunk42 (https://github.com/schmunk42) for bootstrap & model base & extension
4. mdmunir (https://github.com/mdmunir) for JsBlock & Migration Generator (from https://github.com/deesoft/yii2-gii)
