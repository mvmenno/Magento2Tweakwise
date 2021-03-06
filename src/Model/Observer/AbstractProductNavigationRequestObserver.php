<?php
/**
 * Tweakwise & Emico (https://www.tweakwise.com/ & https://www.emico.nl/) - All Rights Reserved
 *
 * @copyright Copyright (c) 2017-2017 Tweakwise.com B.V. (https://www.tweakwise.com)
 * @license   Proprietary and confidential, Unauthorized copying of this file, via any medium is strictly prohibited
 */

namespace Emico\Tweakwise\Model\Observer;

use Emico\Tweakwise\Model\Catalog\Layer\NavigationContext\CurrentContext;
use Emico\Tweakwise\Model\Config;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\HTTP\PhpEnvironment\Response;
use Magento\Framework\UrlInterface;

abstract class AbstractProductNavigationRequestObserver implements ObserverInterface
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var CurrentContext
     */
    protected $context;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var Action
     */
    protected $controller;

    /**
     * CatalogLastPageRedirect constructor.
     *
     * @param Config $config
     * @param CurrentContext $context
     * @param UrlInterface $urlBuilder
     * @param Action $action
     */
    public function __construct(Config $config, CurrentContext $context, UrlInterface $urlBuilder, Action $action)
    {
        $this->config = $config;
        $this->context = $context;
        $this->urlBuilder = $urlBuilder;
        $this->controller = $action;
    }

    /**
     * @return Response|null
     */
    protected function getHttpResponse()
    {
        $response = $this->controller->getResponse();
        if (!$response instanceof Response) {
            return null;
        }

        return $response;
    }

    /**
     * @return bool
     */
    protected function hasTweakwiseResponse()
    {
        if (!$this->config->isLayeredEnabled()) {
            return false;
        }

        if (!$this->context->getContext()->hasResponse()) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Observer $observer)
    {
        $response = $this->getHttpResponse();
        if (!$response) {
            return;
        }

        if (!$this->hasTweakwiseResponse()) {
            return;
        }

        $this->_execute($observer);
    }

    /**
     * @param Observer $observer
     */
    abstract protected function _execute(Observer $observer);
}
