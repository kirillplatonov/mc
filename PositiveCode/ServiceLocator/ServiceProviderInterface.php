<?php
/**
 * PositiveCode Tools
 *
 * @author KpuTuK <bykputuk@ya.ru>
 * @copyright Copyright (c) 2016, PositiveCode Team
 * @license MIT License
 */

namespace System\Kernel\ServiceLocator;

/**
 * Интерфейс реализации сервиса
 * @author KpuTuK <bykputuk@ya.ru>
 * @version 1.0.0
 * @package PositiveCode Tools
 */
interface ServiceProviderInterface
{
    /**
     * Возвращает имя сервиса в локаторе
     * @return string Имя сервиса в локаторе
     */
    public function getServiceName();
    /**
     * Регистрирует обьект локатора в сервисе
     * @param \PositiveCode\ServiceLocator\ServiceLocatorInterface $locator
     */
    public function registerLocator(ServiceLocatorInterface $locator);
}
