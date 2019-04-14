<?php
/**
 * Created by PhpStorm.
 * User: gogl92
 * Date: 17/09/18
 * Time: 01:22 AM
 */
namespace inquid\enhancedgii\repo\components;

class RepoHandler
{
    public $public_repo = false;
    public $github_token   = '';
    public $name;
    public $local_path;
    public $description;
    public $suffix_page;

    public function init($local_path,$name,$github_token,$suffix_page,$description,$public_repo=false){
      $this->local_path = $local_path;
      $this->name=$name;
      $this->github_token = $github_token;
      $this->suffix_page = $suffix_page;
      $this->description = $description;
      $this->public_repo = $public_repo;
    }

    public function createRepo($init_code = false)
        {
            $client = new \Github\Client();
            $client->authenticate($this->github_token, null, \Github\Client::AUTH_HTTP_TOKEN);
            print_r($client->repo()->create(
                $this->name,
                $this->description,
                $this->name . '.' . $this->suffix_page,
                $this->public_repo,
                null,
                true,
                true,
                true,
                null,
                false));
            if ($init_code) {
                exec("git init /{$this->local_path}/{$this->name}/");
                exec("git add -A /{$this->local_path}/{$this->name}/");
                exec("git commit -m \"init\" /{$this->local_path}/{$this->name}/");
                exec("git push /{$this->local_path}/{$this->name}/");
            }
        }

}
