<?php
/**
 * PositiveCode Tools
 *
 * @author KpuTuK <bykputuk@ya.ru>
 * @copyright Copyright (c) 2015, PositiveCode Team
 * @license MIT License
 */

namespace PositiveCode\ServiceLocator;

/**
 * Контейнер сервисов
 * @author KpuTuK <bykputuk@ya.ru>
 * @version 1.0.0
 * @package PositiveCode Tools
 */
class ServiceContainer extends \ArrayObject implements ServiceContainerInterface
{
    public function __construct($input = [])
    {
        parent::__construct($input, \ArrayObject::ARRAY_AS_PROPS);
    }
    /**
     * Возвращает параметр контейнера или сервис 
     * @param string $index Ключ параметра/сервиса
     * @return mixed
     */
    public function offsetGet($index)
    {
        if ($this->offsetExists($index)) {
          return  parent::offsetGet($index);
        } 
        $this->loadToMap($index);
    }
    /**
     * Регистрирует сервис в контейнере
     * @param \PositiveCode\ServiceLocator\ServiceProviderInterface $service Обьект сервиса
     * @param array $bindValues Массив параметров сервиса
     * @return \PositiveCode\ServiceLocator\ServiceContainer 
     */
    public function addServiceProvider(
        ServiceProviderInterface $service, array $bindValues = []
    ) {
        $service->registerContainer($this);
        $this->offsetSet($service->getServiceName(), $service);
        foreach ($bindValues as $key => $value) {
            $this->offsetSet($key, $value);
        }
        return $this;
    }
}
