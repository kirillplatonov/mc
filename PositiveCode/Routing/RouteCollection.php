<?php
/**
 * PositiveCode Tools
 *
 * @author KpuTuK <bykputuk@ya.ru>
 * @copyright Copyright (c) 2016, PositiveCode Team
 * @license MIT License
 */

namespace PositiveCode\Routing;

/**
 * Коллекция роутов
 * @author KpuTuK <bykputuk@ya.ru>
 * @version 1.0.0
 * @package PositiveCode Tools
 */
class RouteCollection extends \ArrayObject {
    /**
     * Конструктор класса
     * @param array $collection Массив рутов
     */
    public function __construct(array $collection = []) {
        parent::__construct($collection);
    }
    /**
     * Возвращает коллекцию роутов в виде массива
     * @return array
     */
    public function getCollection() {
        return $this->getArrayCopy();
    }
    /**
     * Добавляет роут в коллекцию
     * @param string $name Имя роута
     * @param string $pattern Паттерн роута
     * @param string $handler Обработчик роута
     * @param array $methods Методы запроса роута
     */
    public function addRoute($name, $pattern, $handler, array $methods = []) {
        $this->offsetSet($name, new Route($name, $pattern, $handler, $methods));
    }
    /**
     * Добавляет обьект роута в коллецию
     * @param string $index Имя роута
     * @param \PositiveCode\Routing\Route $newval Обьект роута
     * @throws \InvalidArgumentException
     */
    public function offsetSet($index, $newval) {
        if ( ! $newval instanceof Route) {
            throw new \InvalidArgumentException(sprintf(
                'Роут "%s" должен реализовывать System\Kernel\Routing\Route'
            ), $index);
        }
        parent::offsetSet($index, $newval);
    }
}
