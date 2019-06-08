<?php

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $generator yii\gii\generators\form\Generator */

echo '<h2>General</h2>';
echo $form->field($generator, 'appName');
echo $form->field($generator, 'path');
echo $form->field($generator, 'repo')->dropDownList(['https://github.com/yiisoft/yii2-app-basic' => 'yii-basic', 'https://github.com/yiisoft/yii2-app-advanced' => 'yii-advance', 'https://github.com/gogl92/legal' => 'inquid']);
echo $form->field($generator, 'updateDependencies')->checkbox();

echo '<h2>Internalization</h2>';
echo "<div class='row'>";
echo "<div class='col-sm-4'>";
echo $form->field($generator, 'language')->dropDownList(['es' => 'Spanish', 'en' => 'English']);
echo '</div>';
echo "<div class='col-sm-4'>";
echo $form->field($generator, 'time_zone')->dropDownList(['America/Mexico_City', 'America/Chicago', 'America/Los_Angeles', 'America/New_York']);
echo '</div>';
echo "<div class='col-sm-4'>";
echo $form->field($generator, 'date_time_format')->textInput();
echo '</div>';
echo '</div>';
echo "<div class='row'>";
echo "<div class='col-sm-4'>";
echo $form->field($generator, 'thousandSeparator')->textInput();
echo '</div>';
echo "<div class='col-sm-4'>";
echo $form->field($generator, 'decimalSeparator')->textInput();
echo '</div>';
echo "<div class='col-sm-4'>";
echo $form->field($generator, 'currencyCode')->dropDownList(['$', '€', '£', '¥']);
echo '</div>';
echo '</div>';

echo '<h2>Database</h2>';
echo $form->field($generator, 'db_ip_host')->textInput();
echo $form->field($generator, 'db_ip_port')->textInput();
echo $form->field($generator, 'db_prefix')->textInput();
echo $form->field($generator, 'db_username')->textInput();
echo $form->field($generator, 'db_password')->textInput();
echo $form->field($generator, 'db_name')->textInput();
echo $form->field($generator, 'db_ip_host_test')->textInput();
echo $form->field($generator, 'db_ip_port_test')->textInput();
echo $form->field($generator, 'db_name_test')->textInput();

echo '<h2>Google</h2>';
echo $form->field($generator, 'google_project')->textInput();
echo $form->field($generator, 'google_sql_instance_name')->textInput();
echo $form->field($generator, 'google_bucket')->textInput();

echo '<h2>Email</h2>';
echo $form->field($generator, 'email_smtp_host')->textInput();
echo $form->field($generator, 'email_username')->textInput();
echo $form->field($generator, 'email_port')->textInput();
echo $form->field($generator, 'email_password')->textInput();
echo $form->field($generator, 'email_robot')->textInput();
echo $form->field($generator, 'email_encryption')->textInput();

echo '<h2>Github</h2>';
echo $form->field($generator, 'github_client_id')->textInput();
echo $form->field($generator, 'github_client_secret')->textInput();

echo '<h2>User Managment</h2>';
echo $form->field($generator, 'confirm_with')->textInput();
echo $form->field($generator, 'cost')->textInput();
echo $form->field($generator, 'admins')->checkboxList(['admin']);
