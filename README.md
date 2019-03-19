# yii2-enhanced-gii
Yii2 Gii (generator) with Relation

[![Yii2](https://img.shields.io/badge/Powered_by-Yii_Framework-green.svg?style=flat)](http://www.yiiframework.com/)
[![Buy me a Coffee](https://www.buymeacoffee.com/assets/img/custom_images/orange_img.png)](https://www.buymeacoffee.com/inquid)

## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```bash
composer require inquid/yii2-enhanced-gii:dev-master
```

or add

```
"inquid/yii2-enhanced-gii": "dev-master",
```

to the `require` section of your `composer.json` file.

> I separate the mpdf & tree-manager because the package is big & not everyone will use it.

Then you must add this code at your config\main.php.

```php
'modules' => [
      'gridview' => [
          'class' => '\kartik\grid\Module',
      ],
      'datecontrol' => [
          'class' => '\kartik\datecontrol\Module',
      ],
      // If you use tree table
      'treemanager' =>  [
          'class' => '\kartik\tree\Module',
      ]
    ],
```
See gridview settings on http://demos.krajee.com/grid#module

See datecontrol settings on http://demos.krajee.com/datecontrol#module

See treemanager settings on http://demos.krajee.com/tree-manager#module (If you use tree/nested relation table)

## Usage :
Go to your gii tools, and notice the new IO Generator for models & CRUD


# Features
## Model :
1. Generate optimistic lock
2. Generate Timestamp Behaviors
3. Generate Blameable Behavior
4. Generate UUID Behavior
5. Generate Soft Delete Behavior (NEW! Todo : Generate data filtering for relation data, e.g. not show deleted children of hasMany )
6. Specify module destination for files

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
10. Specify module destination for files

## Migration Generator :
1. Generate migration from your database structure (based on : https://github.com/deesoft/yii2-gii)
2. Option to generate with `safeUp()` and `safeDown()`

## Console Commands
1. Configure your console application to use the namespace:
          ```php
          'controllerNamespace' => '@vendor\inquid\yii2-enhanced-gii\console',
          ```
2. Use 
          ```
          ./yii gii
          ```
          ```php
            ./yii gii/enhanced-gii-module --moduleID=bigday --moduleClass=app\\modules\\ModuleName\\Module
          ```

# To Do
1. One-page-CRUD template
2. Implement generator for Soft Delete Behavior (https://github.com/yii2tech/ar-softdelete)

I'm open for any improvement

# Thanks To
1. mootensai (https://github.com/mootensai/yii2-enhanced-gii) for the system structure
2. Jiwanndaru (jiwanndaru@gmail.com) for creating the tradition
3. kartik-v (https://github.com/kartik-v) for most of widgets
4. schmunk42 (https://github.com/schmunk42) for bootstrap & model base & extension
5. mdmunir (https://github.com/mdmunir) for JsBlock & Migration Generator (from https://github.com/deesoft/yii2-gii)
