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
 * Абстрактный класс наследования сервиса
 * @author KpuTuK <bykputuk@ya.ru>
 * @version 1.0.0
 * @package PositiveCode Tools
 */
abstract class ServiceProvider implements ServiceProviderInterface
{
    /**
     * Обьект контейнера
     * @var \PositiveCode\ServiceLocator\ServiceLocatorInterface
     */
    protected $container;
    /**
     * Регистрирует обьект контейнера в сервисе
     * @param \PositiveCode\ServiceLocator\ServiceLocatorInterface $container
     */
    public function registerContainer(ServiceLocatorInterface $container)
    {
        $this->container = $container;
    }
}
