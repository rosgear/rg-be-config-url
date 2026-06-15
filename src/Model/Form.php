<?php
/**
 * Этот файл является частью расширения модуля веб-приложения RosGear.
 * 
 * @link https://rosgear.ru/
 * @copyright Copyright (c) 2015 RosGear
 * @license https://rosgear.ru/license/
 */

namespace Rg\Backend\Config\Url\Model;

use Ge;
use Rg\Backend\Config\Model\ServiceForm;

/**
 * Модель данных конфигурации службы "Маршрутизация".
 * 
 * Cлужба {@see \Ge\Url\UrlManager}, {@see \Ge\Url\UrlRules}.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Rg\Backend\Config\Url\Model
 * @since 1.0
 */
class Form extends ServiceForm
{
    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        parent::init();

        $this->unifiedName = Ge::$app->urlManager->getObjectName();
    }

    /**
     * {@inheritdoc}
     */
    public function maskedAttributes(): array
    {
        return [
            'browserHistory' => 'browserHistory', // история перехода между компонентами
            // Общие правила формирования URL-адреса
            'enablePrettyUrl'     => 'enablePrettyUrl', // ЧПУ для получения красивых URL-адресов
            'enableTrailingSlash' => 'enableTrailingSlash', // слеш в конце URL-адреса
            'showScriptName'      => 'showScriptName', // показывать имя скрипта в URL-адресе
            'routeParam'          => 'routeParam', // параметр запроса URL-адреса
            // Правила формирования постоянных ссылок категорий и статей
            'ruleName'  => 'rule', // имя правила

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function formatterRules(): array
    {
        return [
            [['routeParam'], 'safe'],
            [
                ['browserHistory', 'enablePrettyUrl', 'enableTrailingSlash', 'showScriptName'], 
                 'logic' => [true, false]
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function validationRules(): array
    {
        return [
            [
                ['routeParam', 'ruleName'], 
                'notEmpty'
            ]
        ];
    }

    public function resetConfig(): void
    {
        Ge::$app->unifiedConfig->remove('urlRules');

        parent::resetConfig();
    }

    /**
     * {@inheritdoc}
     */
    public function saveConfig(?array $parameters): static
    {
        $urlManager = $parameters;
        $urlRules   = [];

        if (isset($urlManager['rule'])) {
            $urlRules['rule'] = $urlManager['rule'];
            unset($urlManager['rule']);
        }
        // для службы: \Ge\Url\UrlManager
        Ge::$app->unifiedConfig->{$this->unifiedName} = $urlManager;
        // для службы: \Ge\Url\UrlRules
        if ($urlRules) {
            Ge::$app->unifiedConfig->{'urlRules'} = $urlRules;
        }
        Ge::$app->unifiedConfig->save();
        return $this;
    }
}
