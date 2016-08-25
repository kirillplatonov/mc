<?php
/**
 * PositiveCode Tools
 *
 * @author KpuTuK <bykputuk@ya.ru>
 * @copyright Copyright (c) 2016, PositiveCode Team
 * @license MIT License
 */

namespace PositiveCode\ServiceLocator;

/**
 * Интерфейс реализации локатора сервисов
 * @author KpuTuK <bykputuk@ya.ru>
 * @version 1.0.0
 * @package PositiveCode Tools
 */
interface ServiceLocatorInterface extends \ArrayAccess, \Countable
{
    /**
     * Регистрирует сервис в локаторе
     * @param \PositiveCodeServiceLocator\ServiceProviderInterface $service Обьект сервиса
     * @param array $bindValues Массив параметров сервиса
     * @return \PositiveCode\ServiceLocator 
     */
    public function withServiceProvider(
        ServiceProviderInterface $service, array $bindValues = []
    );
}
