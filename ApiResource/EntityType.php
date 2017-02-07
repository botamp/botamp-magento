<?php
namespace Botamp\Botamp\ApiResource;

class EntityType extends AbstractApiResource
{
    private $configHelper;

    public function __construct(\Botamp\Botamp\Helper\ConfigHelper $configHelper)
    {
        parent::__construct($configHelper);
        $this->configHelper = $configHelper;
    }

    public function createOrUpdate()
    {
        try {
            $entityType = $this->botamp->entityTypes->get('order');
            $this->update($entityType);
        } catch (\Botamp\Exceptions\NotFound $e) {
            $this->create();
        }

        $this->configHelper->setEntityTypeCreated();
    }

    private function create()
    {
        $this->botamp->entityTypes->create([
            'name' => 'order',
            'singular_label' => 'Order',
            'plural_label' => 'Orders',
            'platform' => 'magento'
        ]);
    }

    private function update($entityType)
    {
        $entityTypeAttributes = $entityType->getBody()['data']['attributes'];
        if ($entityTypeAttributes['platform'] !== 'magento') {
            $entityTypeAttributes['platform'] = 'magento';
            $entityTypeId = $entityType->getBody()['data']['id'];

            $this->botamp->entityTypes->update($entityTypeId, $entityTypeAttributes);
        }
    }

    public function created()
    {
        return $this->configHelper->entityTypeCreated();
    }
}
