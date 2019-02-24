<?php
/**
 * Created by PhpStorm.
 * User: gogl92
 * Date: 2019-02-24
 * Time: 01:18
 */
echo "<?php\n";

use yii\helpers\StringHelper; ?>

namespace <?= StringHelper::dirname(ltrim($generator->componentClass, '\\')) ?>;
use Yii;
use <?= ltrim($generator->modelClass, '\\') ?>;
/**
* <?= $componentClass ?> implements all the functionality and business layer of the <?= $modelClass ?> model.
*/
class <?= $componentClass ?> extends Component
{
<?php
echo "\tpublic function beforeSave(){

\t}\n";?>

<?php
echo "\tpublic function afterSave(){

\t}\n";?>
}
