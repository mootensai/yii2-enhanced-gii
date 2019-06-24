# yii2-enhanced-gii
All things needed to create a fully functional application just to modify the business logic to fit your needs 🚀

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
