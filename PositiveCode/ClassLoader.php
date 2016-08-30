<?php
/**
 * PositiveCode Tools
 *
 * @author KpuTuK <bykputuk@ya.ru>
 * @copyright Copyright (c) 2015-2016, PositiveCode Team
 * @license MIT License
 */

namespace PositiveCode;

/**
 * Автоматический загрузчик классов
 * @author KpuTuK
 * @version 1.0.0
 * @package PositiveCode Tools
 */
class ClassLoader
{
    /**
     * Корневая директория классов
     * @var mixed
     */
    protected $rootDir;
    /**
     * Массив классов и пространств имен к ним
     * @var array
     */
    protected $pathes = array();
    /**
     * Конструктор класса
     * @param mixed $rootDir Корневая директория классов
     */
    public function __construct($rootDir = null){
        $this->rootDir = $rootDir;
    }
    /**
     * Доавляет класс и пространство имен в массив
     * @param string $class
     * @param string $namespace
     * @return \PositiveCode\ClassLoader
     */
    public function withPath($class, $namespace)
    {
        $this->pathes[(string)$class] = (string)$namespace;
        return $this;
    }
    /**
     * Добавляет массив классов и пространств имен к ним
     * @param array $pathes Массив классов и пространств имен к ним
     * @return \PositiveCode\ClassLoader
     */
    public function withPathes(array $pathes)
    {
        $this->pathes = array_merge($this->pathes, $pathes);
        return $this;
    }
    /**
     * Преоразует путь классу согласно пространству имен
     * @param string $class Преоразуемый класс
     * @return string Преобразованный класс
     */
    protected function preparePath($class)
    {
        if (count($this->pathes) !== 0) {
            $class = str_replace(
                    array_keys($this->pathes), array_values($this->pathes), $class
            );
        }
        return $class;
    }
    /**
     * Возвращает полный путь к классу
     * @param string $class Искомый класс
     * @return string Полный путь к классу
     * @throws \ErrorException Исключение выбрасываемое в случае отсутствия класса
     */
    protected function getFilePath($class)
    {
        $path = $this->rootDir. $this->preparePath($class);
        if (!strstr($path, '.php')) {
            $path .= '.php';
        }
        $file = str_replace(['\\'], DIRECTORY_SEPARATOR, $path);
        if (file_exists($file)) {
            return $file;
        } else {
            throw new \ErrorException(sprintf('Класс {"%s"} не найден!', $file));
        }
    }
    /**
     * Подключает класс
     * @param string $class Подключаемый класс
     */
    protected function loadClass($class)
    {
        require_once $this->getFilePath($class);
    }
    /**
     * Регистрирует автозагрузчк классов
     * @param booleran $prepend 
     */
    public function register($prepend = false)
    {
        spl_autoload_register(array($this, 'loadClass'), false, $prepend);
    }
    /**
     * Удаляет автозагрузчик классов
     */
    public function unregister()
    {
        spl_autoload_unregister(array($this, 'loadClass'));
    }
}
