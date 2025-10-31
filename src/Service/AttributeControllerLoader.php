<?php

declare(strict_types=1);

namespace TencentCloudDnsBundle\Service;

use Symfony\Bundle\FrameworkBundle\Routing\AttributeRouteControllerLoader;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Routing\RouteCollection;
use TencentCloudDnsBundle\Controller\Admin\AccountCrudController;
use TencentCloudDnsBundle\Controller\Admin\DnsDomainCrudController;
use TencentCloudDnsBundle\Controller\Admin\DnsRecordCrudController;
use Tourze\RoutingAutoLoaderBundle\Service\RoutingAutoLoaderInterface;

#[AutoconfigureTag(name: 'routing.loader')]
class AttributeControllerLoader extends Loader implements RoutingAutoLoaderInterface
{
    private AttributeRouteControllerLoader $controllerLoader;

    private RouteCollection $collection;

    public function __construct()
    {
        parent::__construct();
        $this->controllerLoader = new AttributeRouteControllerLoader();

        $this->collection = new RouteCollection();
        $this->collection->addCollection($this->controllerLoader->load(AccountCrudController::class));
        $this->collection->addCollection($this->controllerLoader->load(DnsDomainCrudController::class));
        $this->collection->addCollection($this->controllerLoader->load(DnsRecordCrudController::class));
    }

    public function load(mixed $resource, ?string $type = null): RouteCollection
    {
        return $this->collection;
    }

    public function supports(mixed $resource, ?string $type = null): bool
    {
        return false;
    }

    public function autoload(): RouteCollection
    {
        return $this->collection;
    }
}
