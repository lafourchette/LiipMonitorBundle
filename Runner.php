<?php

namespace Liip\MonitorBundle;

use InvalidArgumentException;
use ZendDiagnostics\Runner\Reporter\ReporterInterface;
use ZendDiagnostics\Runner\Runner as BaseRunner;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class Runner extends BaseRunner
{
    private $additionalReporters = array();

    /**
     * @param string            $alias
     * @param ReporterInterface $reporter
     */
    public function addAdditionalReporter($alias, ReporterInterface $reporter)
    {
        $this->additionalReporters[$alias] = $reporter;
    }

    /**
     * @param array $aliases
     */
    public function useAdditionalReporters(array $aliases)
    {
        foreach ($this->additionalReporters as $alias => $reporter) {
            if (in_array($alias, $aliases)) {
                $this->addReporter($reporter);
            }
        }
    }

    /**
     * @return array
     */
    public function getAdditionalReporters()
    {
        return $this->additionalReporters;
    }

    public function disableAllChecksExcept(array $checkAlias = array())
    {
        $keys = array_fill_keys($checkAlias, null);
        $copy = $this->checks->getArrayCopy();

        $invalidKeys = array_diff_key($keys, $copy);
        if (count($invalidKeys)) {
            throw new InvalidArgumentException(
                    sprintf('The following aliases does not exist : "%s"', implode('", "', $invalidKeys))
            );
        }

        $this->checks->exchangeArray(array_intersect_key($copy, $keys));
    }
}
