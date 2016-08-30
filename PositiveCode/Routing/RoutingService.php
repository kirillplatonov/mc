<?php

/**
 * PositiveCode Tools
 *
 * @author KpuTuK <bykputuk@ya.ru>
 * @copyright Copyright (c) 2016, PositiveCode Team
 * @license MIT License
 */

namespace PositiveCode\Routing;

use PositiveCode\ServiceLocator\ServiceProvider;

/**
 * Сервис роутинга
 * @author KpuTuK <bykputuk@ya.ru>
 * @version 1.0.0
 * @package PositiveCode Tools
 */
class RoutingService extends ServiceProvider
{

    /**
     * Обьект коллекции роутов
     * @var \PositiveCode\Routing\RouteCollection 
     */
    protected $collection;
    /**
     * Полный путь к классу кеша
     * @var string
     */
    protected $cachePath;
    /**
     * Пространство имен класса кеша
     * @var string
     */
    protected $cacheNamespace;
    /**
     * Создает обьект сервиса роутинга с указанными параметрами
     * @param \PositiveCode\Routing\RouteCollection $collection Обьект коллекции роутов
     * @param string $cachePath Полный путь к классу кеша
     * @param string $cacheNamespace Пространство имен класса кеша
     */
    public function __construct(
        RouteCollection $collection, $cachePath, $cacheNamespace
    ) {
        $this->cachePath = $cachePath;
        $this->cacheNamespace = $cacheNamespace;
        $this->collection = $collection;
    }
    /**
     * Возвращает имя сервиса
     * @return string
     */
    public function getServiceName()
    {
        return 'routing';
    }
    /**
     * Обрабатывает uri по зарание заданному роуту
     * @param string $uri Uri
     * @param string $method Метод запроса
     * @return array Массив данных роута
     */
    public function match($uri, $method = 'GET')
    {
        if ( ! file_exists($this->cachePath)) {
            (new RouteDumper(
                $this->collection, $this->cachePath, $this->cacheNamespace
            ))->dumpClass();
        }
        $class = $this->cacheNamespace . '\\' . basename($this->cachePath, '.php');
        return (new $class())->match($uri, $method);
    }

}
