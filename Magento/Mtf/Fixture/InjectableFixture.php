<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Fixture;

use Magento\Mtf\Handler\HandlerFactory;
use Magento\Mtf\Repository\RepositoryFactory;
use Magento\Mtf\Config\DataInterface;
use Magento\Mtf\System\Event\EventManagerInterface;
use Magento\Mtf\Fixture\InjectableFixture\Replacer;

/**
 * Ensures correct data representation between the system under test and testing framework.
 *
 * @api
 */
class InjectableFixture implements FixtureInterface
{
    /**
     * Default data set name.
     *
     * @var array
     */
    protected $defaultDataSetName = 'default';

    /**
     * Fixture data.
     *
     * @var array
     */
    protected $data = [];

    /**
     * Fixture configuration.
     *
     * @var array
     */
    protected $dataConfig = [];

    /**
     * Repository class name.
     *
     * @var string
     */
    protected $repositoryClass;

    /**
     * Configuration instance.
     *
     * @var DataInterface
     */
    protected $configuration;

    /**
     * Repository factory instance.
     *
     * @var RepositoryFactory
     */
    protected $repositoryFactory;

    /**
     * Fixture factory instance.
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * Handler factory instance.
     *
     * @var HandlerFactory
     */
    protected $handlerFactory;

    /**
     * Handler interface Name.
     *
     * @var string
     */
    protected $handlerInterface;

    /**
     * Event manager instance.
     *
     * @var \Magento\Mtf\System\Event\EventManagerInterface
     */
    protected $eventManager;

    /**
     * Class responsible for replace data in fields.
     *
     * @var Replacer
     */
    protected $replacer;

    /**
     * Constructor
     *
     * @constructor
     * @param DataInterface $configuration
     * @param RepositoryFactory $repositoryFactory
     * @param FixtureFactory $fixtureFactory
     * @param HandlerFactory $handlerFactory
     * @param EventManagerInterface $eventManager
     * @param Replacer $replacer
     * @param array $data
     * @param string $dataset
     * @param bool $persist
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function __construct(
        DataInterface $configuration,
        RepositoryFactory $repositoryFactory,
        FixtureFactory $fixtureFactory,
        HandlerFactory $handlerFactory,
        EventManagerInterface $eventManager,
        Replacer $replacer,
        array $data = [],
        $dataset = '',
        $persist = false
    ) {
        $this->configuration = $configuration;
        $this->repositoryFactory = $repositoryFactory;
        $this->fixtureFactory = $fixtureFactory;
        $this->handlerFactory = $handlerFactory;
        $this->eventManager = $eventManager;
        $this->replacer = $replacer;

        if ($dataset) {
            $data = $this->getDataFromRepository($dataset, $data);
        }
        if (!$data) {
            $data = $this->getDataFromRepository($this->defaultDataSetName);
        }

        // todo This code should be removed together with removing uniqueness of ConfigData fixture.
        if (isset($this->section) && is_array(current($data))) {
            $this->data['section'] = $data;
            $this->data = $this->replacer->apply($this->data);
            if ($persist === true) {
                $this->persist();
            }
            return;
        }

        foreach ($data as $name => $value) {
            if (!isset($this->$name)) {
                continue;
            }

            $params = $this->$name;
            if ($value === null) {
                $value = isset($params['default_value']) ? $params['default_value'] : null;
            }

            $source = $this->getSourceParam($params);
            if ($source) {
                $value = $this->prepareSource($name, $value, $source);
            } else {
                if (isset($params['repository'])) {
                    $value = $this->getDataFromFieldRepository($value, $params['repository']);
                }
                $value = $this->skipEmptyValue($value);
            }

            if (null !== $value) {
                $this->data[$name] = $value;
            }
        }

        $this->data = $this->replacer->apply(['section' => $this->data])['section'];
        if ($persist === true) {
            $this->persist();
        }
    }

    /**
     * Skip empty value of fixture data.
     *
     * @param mixed $value
     * @return mixed
     */
    protected function skipEmptyValue($value)
    {
        if ('-' === $value) {
            return null;
        }
        if (is_array($value)) {
            $result = [];
            foreach ($value as $key => $subValue) {
                $data = $this->skipEmptyValue($subValue);
                if ($data !== null) {
                    $result[$key] = $data;
                }
            }
            return empty($result) ? null : $result;
        }
        return $value;
    }

    /**
     * Prepare source data.
     *
     * @param string $fieldName
     * @param mixed $value
     * @param array $source
     * @return mixed
     */
    protected function prepareSource($fieldName, $value, array $source)
    {
        $value = $this->skipEmptyValue($value);
        if (null !== $value) {
            $params = &$this->$fieldName;
            $fixture = $this->fixtureFactory->create(
                $source['source'],
                [
                    'data' => $value,
                    'params' => $params,
                    'persist' => true
                ]
            );
            $params[$source['field']] = $fixture;
            $value = $fixture->getData();
        }
        return $value;
    }

    /**
     * Return source param.
     *
     * @param array $params
     * @return null|array
     */
    protected function getSourceParam(array $params)
    {
        $param = null;
        if (isset($params['source'])) {
            $param['field'] = 'source';
            $param['source'] = $params['source'];
        }
        return $param;
    }

    /**
     * Persists Fixture Data into application.
     *
     * @return void
     */
    public function persist()
    {
        $this->eventManager->dispatchEvent(['persist_before'], [get_class($this)]);
        if (!empty($this->handlerInterface)) {
            $result = $this->handlerFactory->create($this->handlerInterface)->persist($this);
            if (!empty($result)) {
                foreach ($result as $key => $value) {
                    $this->data[$key] = $value;
                }
            }
        }
        $this->eventManager->dispatchEvent(['persist_after'], [get_class($this)]);
    }

    /**
     * Return prepared data set.
     *
     * @param string $key [optional]
     * @return mixed
     * @throws \RuntimeException
     */
    public function getData($key = null)
    {
        if (empty($this->data)) {
            throw new \RuntimeException('Data must be set');
        }
        if ($key === null) {
            return $this->data;
        }
        /* process a/b/c key as ['a']['b']['c'] */
        if (strpos($key, '/')) {
            $data = $this->getDataByPath($key);
        } else {
            $data = $this->getDataByKey($key);
        }
        return $data;
    }

    /**
     * Verify whether key is present in fixture data.
     *
     * @param string $key [optional]
     * @return bool
     */
    public function hasData($key = null)
    {
        if (empty($this->data)) {
            return false;
        } elseif ($key === null) {
            return true;
        }
        /* process a/b/c key as ['a']['b']['c'] */
        if (strpos($key, '/')) {
            return $this->getDataByPath($key) === null ? false : true;
        } else {
            return $this->getDataByKey($key) === null ? false : true;
        }
    }

    /**
     * Return data field configuration.
     *
     * @param string $key
     * @return array
     * @throws \RuntimeException
     */
    public function getDataFieldConfig($key)
    {
        if (!isset($this->$key)) {
            throw new \RuntimeException(sprintf('Data key "%s" is missed', $key));
        }
        return $this->$key;
    }

    /**
     * Return data set configuration settings.
     *
     * @return array
     */
    public function getDataConfig()
    {
        return $this->dataConfig;
    }

    /**
     * Find data from repository by data set name.
     *
     * @param string $dataset
     * @param array $data
     * @return array
     */
    public function getDataFromRepository($dataset, array $data = [])
    {
        if (empty($this->repositoryClass)) {
            return $result = $data;
        } else {
            $repository = $this->repositoryFactory->get($this->repositoryClass);
            return $result = array_replace_recursive($repository->get($dataset), $data);
        }
    }

    /**
     * Find data from field repository by data set name.
     *
     * @param mixed $value
     * @param string $fieldRepositoryClass
     * @return mixed
     */
    protected function getDataFromFieldRepository($value, $fieldRepositoryClass)
    {
        if (isset($value['dataset'])) {
            $data = $this->repositoryFactory->get($fieldRepositoryClass)->get($value['dataset']);
            unset($value['dataset']);
            return array_replace_recursive($data, $value);
        } else {
            return $value;
        }
    }

    /**
     * Get object data by path.
     *
     * Method consider the path as chain of keys: a/b/c => ['a']['b']['c']
     *
     * @param string $path
     * @return mixed
     */
    protected function getDataByPath($path)
    {
        $keys = explode('/', $path);
        $data = $this->data;
        foreach ($keys as $key) {
            if (is_array($data) && isset($data[$key])) {
                $data = $data[$key];
            } else {
                return null;
            }
        }
        return $data;
    }

    /**
     * Get object data by particular key.
     *
     * @param string $key
     * @return mixed
     */
    protected function getDataByKey($key)
    {
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }
}
