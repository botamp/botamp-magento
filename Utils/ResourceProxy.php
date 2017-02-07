<?php
namespace Botamp\Botamp\Utils;

class ResourceProxy
{

    private $allResources;
    private $currentResource;
    private $sessionHelper;
    private $entityType;

    public function __construct(
        \Botamp\Botamp\ApiResource\Contact $contact,
        \Botamp\Botamp\ApiResource\Me $me,
        \Botamp\Botamp\ApiResource\OrderEntity $orderEntity,
        \Botamp\Botamp\ApiResource\ProductEntity $productEntity,
        \Botamp\Botamp\ApiResource\Subscription $subscription,
        \Botamp\Botamp\ApiResource\EntityType $entityType,
        \Botamp\Botamp\Helper\SessionHelper $sessionHelper
    ) {
        $this->allResources = [
            'contact' => $contact,
            'me' => $me,
            'order_entity' => $orderEntity,
            'product_entity' => $productEntity,
            'subscription' => $subscription
        ];
        $this->entityType = $entityType;
        $this->sessionHelper = $sessionHelper;
    }

    public function setCurrentResource($currentResourceCode)
    {
        $this->currentResource = $this->allResources[$currentResourceCode];
    }

    public function __call($method, $arguments)
    {
        if ($this->currentResource == $this->allResources['order_entity']) {
            if (!$this->entityType->created()) {
                $this->entityType->createOrUpdate();
            }
        }

        $backendSession = $this->sessionHelper->getSessionObject('backend');
        $backendSession->setBotampAuthStatus('ok');
        try {
            // @codingStandardsIgnoreStart
            return call_user_func_array([$this->currentResource, $method], $arguments);
            // @codingStandardsIgnoreEnd
        } catch (\Botamp\Exceptions\Unauthorized $e) {
            $backendSession->setBotampAuthStatus('unauthorized');
        }
    }
}
