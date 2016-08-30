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
 * Дампер роутов
 * @author KpuTuK <bykputuk@ya.ru>
 * @author Fabien Potencier <fabien@symfony.com>
 * @version 1.0.0
 * @package PositiveCode Tools
 */
class RouteDumper {
    /**
     * Пространство имен класса кэша
     * @var string
     */
     protected $namespace;
    /**
     * Полный путь к классу кэша
     * @var string
     */
     protected $path;
    /**
     * Обьект коллекции роутов
     * @var PositiveCode\Routing\RouteCollection
     */
    protected $collection;
    /**
     * Создает экземпля дампера с указанным обьектом коллекции
     * @param \PositiveCode\Routing\RouteCollection $collection
     * @param string $path Полный путь к классу кэша
     * @param string $namespace Пространство имен класса кэша
     */
    public function __construct(RouteCollection $collection, $path, $namespace = '') {
        $this->collection = $collection;
        $this->path = $path;
        $this->namespace = $namespace;
    }
    /**
     * Создает дамп класса
     */
    public function dumpClass() {
        $time = (new \DateTime)->format('D, d M Y H:i:s');
        $read = <<<EOF
<?php
namespace {$this->namespace};
/**
 * Create by RouteDumper {$time}
 * Кеш роутов
 */
class RouteCacheMather {
    public function getCachedRoutes() {
        return [
            {$this->generateNames()}
        ];
    }
    public function match(\$uri, \$method) {
        \$matches = [];
        \$pathInfo = \$uri;
        {$this->dumpRoutes()}
        return ['name' => 404, 'handler' => '\System\Kernel\Controller@requestError', 'params' => [\$uri]];
    }
    protected function filterParams(\$params) {
        return array_filter(\$params, function (\$param) {
            return ! is_int(\$param);
        }, ARRAY_FILTER_USE_KEY);
    }  
}
EOF;
        file_put_contents($this->path, $read);
    }
    /**
     * Генерирует строку массива имен роутов
     * @return string
     */
    protected function generateNames() {
        $write = '';
        foreach (array_keys($this->collection->getCollection()) as $name) {
            $write .= "\t'$name',\n";
        }
        return $write;
    }
    /**
     * Генерирует строку роутов
     * @return string
     */
    protected function dumpRoutes() {
        foreach ($this->collection->getCollection() as $route) {
            $compile = $route->compile();
            if (true === $compile['match']) {
                $write .= $this->dumpRouteMatcher(
                    $route['name'],
                    $compile['pattern'],
                    $route['handler'],
                    $route['methods'],
                    $route['params']
                );
                continue;
            }
            $write .= $this->dumpRoute(
                $route['name'],
                $compile['pattern'],
                $route['handler'],
                $route['methods'],
                $route['params']
            );
        }
        return $write;
    }
    /**
     * Создает дамп роута
     * @param string $name Имя роута
     * @param string $pattern Паттерн роута
     * @param string $handler Обработчик роута
     * @param array $methods Методы запроса роута
     * @param array $params Масиив параметров роута
     * @return string
     */
    protected function dumpRoute($name, $pattern, $handler, array $methods, array $params = []) {
        if (is_array($methods)) {
            $methods = implode("', '", $methods);
        }
        if ( ! empty($methods)) {
return <<<EOF
        
\t/** Create for "{$name}" route **/
\tif (in_array(\$method, ['{$methods}'])) {
\t    if (\$pathInfo === '{$pattern}') {
\t        return ['name' => '{$name}', 'handler' => '{$handler}', 'params' => [{$this->createParams($params)}]];
\t    }
\t}

EOF;
        } 
return <<<EOF
        
\t/** Create for "{$name}" route **/
\tif (\$pathInfo === '{$pattern}') {
\t    return ['name' => '{$name}', 'handler' => '{$handler}', 'params' => [{$this->createParams($params)}]];
\t}

EOF;
    }
    /**
     * Создает дамп prce роута
     * @param string $name Имя роута
     * @param string $pattern Паттерн роута
     * @param string $handler Обработчик роута
     * @param array $methods Методы запроса роута
     * @param array $params Масиив параметров роута
     * @return string
     */
    protected function dumpRouteMatcher($name, $pattern, $handler, array $methods, array $params = []) {
        if (is_array($methods)) {
            $methods = implode("', '", $methods);
        }
        if ( ! empty($methods)) {
return <<<EOF
        
\t/** Create for "{$name}" route **/
\tif (in_array(\$method, ['{$methods}'])) {
\t    if (preg_match('#^{$pattern}$#s', \$pathInfo, \$matches)) {
\t        return ['name' => '{$name}', 'handler' => '{$handler}', 'params' => array_merge(\$this->filterParams(\$matches), [{$this->createParams($params)}])];
\t    }
\t}

EOF;
        } 
return <<<EOF
        
\t/** Create for "{$name}" route **/
\tif (preg_match('#^{$pattern}$#s', \$pathInfo, \$matches)) {
\t    return ['name' => '{$name}', 'handler' => '{$handler}', 'params' => array_merge(\$this->filterParams(\$matches), [{$this->createParams($params)}])];
\t}

EOF;
    }
    /**
     * Генерирует строку массива параметров
     * @param array $params Массив параметров
     * @return string
     */
    protected function createParams(array $params = []) {
        $write = '';
        foreach ($params as $key => $value) {
            $write .= '\''.$key.'\' => \''.$value.'\', ';
}
        return $write;
    }
}
