<?php
namespace yiiunit\gii\generators;

use yii\gii\generators\model\Generator as ModelGenerator;
use yiiunit\gii\GiiTestCase;

/**
 * ModelGeneratorTest checks that Gii model generator produces valid results
 * @group gii
 */
class ModelGeneratorTest extends GiiTestCase
{
    public function testAll()
    {
        $generator = new ModelGenerator();
        $generator->template = 'default';
        $generator->tableName = '*';

        $generator->queryNs = 'application\models';

        $valid = $generator->validate();
        $this->assertFalse($valid);
        $this->assertEquals($generator->getFirstError('queryNs'), 'Namespace must be associated with an existing directory.');

        $generator->queryNs = 'app\models';

        $valid = $generator->validate();
        $this->assertTrue($valid, 'Validation failed: ' . print_r($generator->getErrors(), true));

        $files = $generator->generate();
        $this->assertEquals(8, count($files));
        $expectedNames = [
            'Attribute.php',
            'Category.php',
            'CategoryPhoto.php',
            'Customer.php',
            'Product.php',
            'ProductLanguage.php',
            'Profile.php',
            'Supplier.php',
        ];
        $fileNames = array_map(function ($f) {
            return basename($f->path);
        }, $files);
        sort($fileNames);
        $this->assertEquals($expectedNames, $fileNames);
    }

    /**
     * @return array
     */
    public function relationsProvider()
    {
        return [
            ['category', 'Category.php', [
                [
                    'name' => 'function getCategoryPhotos()',
                    'relation' => "\$this->hasMany(CategoryPhoto::className(), ['category_id' => 'id']);",
                    'expected' => true,
                ],
                [
                    'name' => 'function getProduct()',
                    'relation' => "\$this->hasOne(Product::className(), ['category_id' => 'id', 'category_language_code' => 'language_code']);",
                    'expected' => true,
                ],
            ]],
            ['category_photo', 'CategoryPhoto.php', [
                [
                    'name' => 'function getCategory()',
                    'relation' => "\$this->hasOne(Category::className(), ['id' => 'category_id']);",
                    'expected' => true,
                ],
            ]],
            ['supplier', 'Supplier.php', [
                [
                    'name' => 'function getProducts()',
                    'relation' => "\$this->hasMany(Product::className(), ['supplier_id' => 'id']);",
                    'expected' => true,
                ],
                [
                    'name' => 'function getAttributes0()',
                    'relation' => "\$this->hasMany(Attribute::className(), ['supplier_id' => 'id']);",
                    'expected' => true,
                ],
                [
                    'name' => 'function getAttributes()',
                    'relation' => "\$this->hasOne(Attribute::className(), ['supplier_id' => 'id']);",
                    'expected' => false,
                ],
                [
                    'name' => 'function getProductLanguage()',
                    'relation' => "\$this->hasOne(ProductLanguage::className(), ['supplier_id' => 'id']);",
                    'expected' => true,
                ],
            ]],
            ['product', 'Product.php', [
                [
                    'name' => 'function getSupplier()',
                    'relation' => "\$this->hasOne(Supplier::className(), ['id' => 'supplier_id']);",
                    'expected' => true,
                ],
                [
                    'name' => 'function getCategory()',
                    'relation' => "\$this->hasOne(Category::className(), ['id' => 'category_id', 'language_code' => 'category_language_code']);",
                    'expected' => true,
                ],
                [
                    'name' => 'function getProductLanguage()',
                    'relation' => "\$this->hasOne(ProductLanguage::className(), ['supplier_id' => 'supplier_id', 'id' => 'id']);",
                    'expected' => true,
                ],
            ]],
            ['product_language', 'ProductLanguage.php', [
                [
                    'name' => 'function getSupplier()',
                    'relation' => "\$this->hasOne(Product::className(), ['supplier_id' => 'supplier_id', 'id' => 'id']);",
                    'expected' => true,
                ],
                [
                    'name' => 'function getSupplier0()',
                    'relation' => "\$this->hasOne(Supplier::className(), ['id' => 'supplier_id']);",
                    'expected' => true,
                ],
            ]],
        ];
    }

    /**
     * @dataProvider relationsProvider
     * @param $tableName string
     * @param $fileName string
     * @param $relations array
     */
    public function testRelations($tableName, $fileName, $relations)
    {
        $generator = new ModelGenerator();
        $generator->template = 'default';
        $generator->generateRelationsFromCurrentSchema = false;
        $generator->tableName = $tableName;

        $files = $generator->generate();
        $this->assertEquals(1, count($files));
        $this->assertEquals($fileName, basename($files[0]->path));

        $code = $files[0]->content;
        foreach ($relations as $relation) {
            $found = strpos($code, $relation['relation']) !== false;
            $this->assertTrue(
                $relation['expected'] === $found,
                "Relation \"{$relation['relation']}\" should"
                . ($relation['expected'] ? '' : ' not')." be there:\n" . $code
            );

            $found = strpos($code, $relation['name']) !== false;
            $this->assertTrue(
                $relation['expected'] === $found,
                "Relation Name \"{$relation['name']}\" should"
                . ($relation['expected'] ? '' : ' not')." be there:\n" . $code
            );
        }
    }

    public function testSchemas()
    {
    }

    /**
     * @return array
     */
    public function rulesProvider()
    {
        return [
            ['category_photo', 'CategoryPhoto.php', [
                "[['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::className(), 'targetAttribute' => ['category_id' => 'id']],",
                "[['category_id', 'display_number'], 'unique', 'targetAttribute' => ['category_id', 'display_number']],",
            ]],
            ['product', 'Product.php', [
                "[['supplier_id'], 'exist', 'skipOnError' => true, 'targetClass' => Supplier::className(), 'targetAttribute' => ['supplier_id' => 'id']],",
                "[['category_id', 'category_language_code'], 'exist', 'skipOnError' => true, 'targetClass' => Category::className(), 'targetAttribute' => ['category_id' => 'id', 'category_language_code' => 'language_code']],",
                "[['category_id', 'category_language_code'], 'unique', 'targetAttribute' => ['category_id', 'category_language_code']],"
            ]],
            ['product_language', 'ProductLanguage.php', [
                "[['supplier_id', 'id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::className(), 'targetAttribute' => ['supplier_id' => 'supplier_id', 'id' => 'id']],",
                "[['supplier_id'], 'exist', 'skipOnError' => true, 'targetClass' => Supplier::className(), 'targetAttribute' => ['supplier_id' => 'id']],",
                "[['supplier_id'], 'unique']",
                "[['id', 'supplier_id', 'language_code'], 'unique', 'targetAttribute' => ['id', 'supplier_id', 'language_code']]",
                "[['id', 'supplier_id'], 'unique', 'targetAttribute' => ['id', 'supplier_id']]",
            ]],
        ];
    }

    /**
     * @dataProvider rulesProvider
     *
     * @param $tableName string
     * @param $fileName string
     * @param $rules array
     */
    public function testRules($tableName, $fileName, $rules)
    {
        $generator = new ModelGenerator();
        $generator->template = 'default';
        $generator->tableName = $tableName;

        $files = $generator->generate();
        $this->assertEquals(1, count($files));
        $this->assertEquals($fileName, basename($files[0]->path));

        $code = $files[0]->content;
        foreach ($rules as $rule) {
            $location = strpos($code, $rule);
            $this->assertTrue($location !== false,
                "Rule \"{$rule}\" should be there:\n" . $code
            );
        }
    }

    public function testGenerateStandardizedCapitalsForClassNames()
    {
        $modelGenerator = new ModelGeneratorMock;
        $modelGenerator->standardizeCapitals = true;

        $tableNames = [
            'lower_underline_name' => 'LowerUnderlineName',
            'Ucwords_Underline_Name' => 'UcwordsUnderlineName',
            'UPPER_UNDERLINE_NAME' => 'UpperUnderlineName',
            'lower-hyphen-name' => 'LowerHyphenName',
            'Ucwords-Hyphen-Name' => 'UcwordsHyphenName',
            'UPPER-HYPHEN-NAME' => 'UpperHyphenName',
            'CamelCaseName' => 'CamelCaseName',
            'lowerUcwordsName' => 'LowerUcwordsName',
            'lowername' => 'Lowername',
            'UPPERNAME' => 'Uppername',
        ];

        foreach ($tableNames as $tableName => $expectedClassName) {
            $generatedClassName = $modelGenerator->publicGenerateClassName($tableName);
            $this->assertEquals($expectedClassName, $generatedClassName);
        }
    }

    public function testGenerateNotStandardizedCapitalsForClassNames()
    {
        $modelGenerator = new ModelGeneratorMock;
        $modelGenerator->standardizeCapitals = false;

        $tableNames = [
            'lower_underline_name' => 'LowerUnderlineName',
            'Ucwords_Underline_Name' => 'UcwordsUnderlineName',
            'UPPER_UNDERLINE_NAME' => 'UPPERUNDERLINENAME',
            'ABBRMyTable' => 'ABBRMyTable',
            'lower-hyphen-name' => 'Lower-hyphen-name',
            'Ucwords-Hyphen-Name' => 'Ucwords-Hyphen-Name',
            'UPPER-HYPHEN-NAME' => 'UPPER-HYPHEN-NAME',
            'CamelCaseName' => 'CamelCaseName',
            'lowerUcwordsName' => 'LowerUcwordsName',
            'lowername' => 'Lowername',
            'UPPERNAME' => 'UPPERNAME',
            'PARTIALUpperName' => 'PARTIALUpperName',
        ];

        foreach ($tableNames as $tableName => $expectedClassName) {
            $generatedClassName = $modelGenerator->publicGenerateClassName($tableName);
            $this->assertEquals($expectedClassName, $generatedClassName);
        }
    }
}
