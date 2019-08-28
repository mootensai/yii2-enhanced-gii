<?php

declare(strict_types=1);

namespace inquid\enhancedgii\domain;

use inquid\godaddy\Godaddy;
use Yii;

/**
 * This generator will generate migration file for the specified database table.
 *
 * @author Inquid INC <contact@inquid.co>
 *
 * @since 0.9
 */
class Generator extends \yii\gii\Generator
{
    public $godaddy_key;
    public $godaddy_secret;
    public $domain;
    public $ip;
    public $name = 'subdomain';
    public $ttl;
    public $type;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'INQUID Generator (Domains)';
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'This generator adds a record (subdomains) or purchase a domain using the godaddy api';
    }

    /**
     * {@inheritdoc}
     */
    public function requiredTemplates()
    {
        return ['README.md'];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['name'], 'filter', 'filter' => 'trim'],
            [['ttl'], 'integer'],
            [['godaddy_key', 'godaddy_secret', 'domain', 'ip', 'type'], 'string'],
            [['godaddy_key', 'godaddy_secret'], 'required'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'name'           => 'subdomain',
            'ttl'            => 'TTL',
            'godaddy_key'    => 'Godaddy Key',
            'godaddy_secret' => 'Godaddy Secret',
            'domain'         => 'domain',
            'ip'             => 'IP',
            'type'           => 'type',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function hints()
    {
        return array_merge(parent::hints(), [
            'name'           => 'subdomain',
            'ttl'            => 'TTL',
            'godaddy_key'    => 'Godaddy Key',
            'godaddy_secret' => 'Godaddy Secret',
            'domain'         => 'domain',
            'ip'             => 'IP',
            'type'           => 'type',
        ]);
    }

    public function generate()
    {
        $files = [];
        $godaddyClient = new Godaddy();
        $godaddyClient->apiKey = $this->godaddy_key;
        $godaddyClient->apiSecret = $this->godaddy_secret;
        $response = $godaddyClient->getDomainAviability(['domain' => $this->domain]);
        Yii::debug(json_encode($response));
        Yii::debug('Creating the domain'.$this->domain);

        return $files;
    }
}
