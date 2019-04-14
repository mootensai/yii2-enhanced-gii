<?php

namespace inquid\enhancedgii\repo;

use inquid\enhancedgii\repo\components\RepoHandler;
use Yii;
use yii\gii\CodeFile;

/**
 * This generator will generate migration file for the specified database table.
 *
 * @author Inquid INC <contact@inquid.co>
 * @since 0.9
 */
class Generator extends \yii\gii\Generator
{
    public $public_repo = false;
    public $github_token = '';
    public $name;
    public $local_path;
    public $description;
    public $suffix_page;


    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'INQUID Generator (Repository)';
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return 'This generator Creates a new repository and uploads it to a repository service provider like github, gitlab or bitbucket';
    }

    /**
     * {@inheritdoc}
     */
    public function requiredTemplates()
    {
        return ['README.md'];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['public_repo', 'github_token', 'local_path', 'suffix_page'], 'filter', 'filter' => 'trim'],
            [['public_repo'], 'boolean'],
            [['name'], 'required']
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'public_repo' => 'Is this a public repository?',
            'github_token' => 'Given Github token',
            'name' => 'Repository Name',
            'local_path' => 'Local Repository path destination',
            'description' => 'Repository Description',
            'suffix_page' => 'Suffix Page'
        ]);
    }

    /**
     * @inheritdoc
     */
    public function hints()
    {
        return array_merge(parent::hints(), [
            'public_repo' => 'Is this a public repository?',
            'github_token' => 'Given Github token',
            'name' => 'Repository Name',
            'local_path' => 'Local Repository path destination',
            'description' => 'Repository Description',
            'suffix_page' => 'Suffix Page'
        ]);
    }

    public function generate()
    {
        $files = [];
        $repoHandler = new RepoHandler($this->local_path, $this->name, $this->github_token, $this->suffix_page, $this->description, $this->public_repo);
        $files[] = new CodeFile("{$this->local_path}/{$this->name}/README.md", $this->render('README.md', ['repo_name' => 'name']));
        Yii::debug('Creating the repo' . $repoHandler->createRepo(true));
        return $files;
    }
}
