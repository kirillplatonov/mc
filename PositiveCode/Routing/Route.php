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
 * Обьект роута
 * @author KpuTuK <bykputuk@ya.ru>
 * @version 1.0.0
 * @package PositiveCode Tools
 */
class Route extends \ArrayObject {
    protected $patterns = [
        'i' => '[0-9]+',
        's' => '[a-zA-Z0-9\.\-_%]+'
    ];
    /**
     * Создает обьект роута с указанными параметрами
     * @param string $name Имя роута
     * @param string $pattern prce паттерн обработки
     * @param string $handler Обработчик роута вида класс@метод
     * @param array $methods Массив методов запроса роута
     * @param array $params Дополнительные параметры передаваемые в экшен
     */
    public function __construct(
        $name, 
        $pattern, 
        $handler, 
        array $methods = [],
        array $params = []) {
        parent::__construct([
            'name' => $name,
            'pattern' => $pattern,
            'handler' => $handler,
            'methods' => $methods,
            'params' => $params
        ]);
    }
    /**
     * Схраняет параметр роута
     * @param string $index Имя параметра
     * @param mixed $newval Значение параметра
     */
    public function offsetSet($index, $newval) {
        $this->validateIndex($index);
        parent::offsetSet($index, $newval);
    }
    /**
     * Проверяет наличие параметра роута 
     * @param string $index Имя параметра
     */
    public function offsetExists($index) {
        $this->validateIndex($index);
        parent::offsetExists($index);
    }
    /**
     * Взвращает параметр роута
     * @param string $index Имя параметра
     * @return mixed Значение параметра
     */
    public function offsetGet($index) {
        $this->validateIndex($index);
        return parent::offsetGet($index);
    }
    /**
     * Удаляет параметр роута
     * @param string $index Имя параметра
     */
    public function offsetUnset($index) {
        $this->validateIndex($index);
        parent::offsetUnset($index);
    }
    /**
     * Компилирует prce шаблон роута и возвращает массив данных роута
     * @return array
     */
    public function compile() {
        if (false === strpos($this['pattern'], '{')) {
            return [
                'pattern' => $this['pattern'],
                'match' => false, 
                'params' => $this['params']
            ];
        }
        $route = $this;
        return [ 
            'pattern' => rtrim(preg_replace_callback('#\{(\w+):(\w+)\}#', 
                function($match) use ($route) {
                    list(, $name, $prce) = $match;
                    return '(?<'.$name.'>'.strtr($prce, $route->patterns).')';
                }, $this['pattern']), 
            '/'),
            'match' => true,
            'params' => $this['params']
        ];
    }
    /**
     * Прверяет имя параметра
     * @param string $index Имя параметра
     * @throws \InvalidArgumentException
     */
    protected function validateIndex($index) {
        if ( ! in_array(
            $index, ['name', 'pattern', 'handler', 'methods', 'params']
        )) {
            throw new \InvalidArgumentException(sprintf(
                'Ожидался параметр "%s" вместо "%s"!', 
                implode(
                    '|', ['name', 'pattern', 'handler', 'methods', 'params']
                ),
                $index
            ));
        }
    }
}

