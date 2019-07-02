<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 7/2/19
 * Time: 10:08 AM
 */

namespace AE\SalesforceRestSdk\Rest;


use Psr\Http\Message\RequestInterface;

trait CallOptionsTrait
{
    /**
     * @var array
     */
    protected $callOptions = [];

    protected function appendSforceCallOptions(RequestInterface $request): RequestInterface
    {
        $callOptions = array_reduce(
            array_keys($this->callOptions),
            function (array $carry, $key) {
                $value = $this->callOptions[$key];
                if (strlen($value) > 0) {
                    $carry[] = "$key=$value";
                }

                return $carry;
            },
            []
        );

        if (!empty($callOptions)) {
            return $request->withAddedHeader('Sforce-Call-Options', implode(" ", $callOptions));
        }

        return $request;
    }

    /**
     * @param array $options
     *
     * @return self
     */
    public function setCallOptions(array $options): self
    {
        $this->callOptions = $options;

        return $this;
    }

    /**
     * @param $name
     * @param string $value
     *
     * @return self
     */
    public function setCallOption($name, string $value): self
    {
        $this->callOptions[$name] = $value;

        return $this;
    }

    /**
     * @param $name
     *
     * @return self
     */
    public function removeCallOption($name): self
    {
        unset($this->callOptions[$name]);

        return $this;
    }
}