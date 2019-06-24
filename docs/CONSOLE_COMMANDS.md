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