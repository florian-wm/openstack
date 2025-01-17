<?php



namespace OpenStack\Identity\v2\Models;

use OpenStack\Common\Resource\Alias;
use OpenStack\Common\Resource\OperatorResource;
use OpenStack\Common\Transport\Utils;
use Psr\Http\Message\ResponseInterface;

/**
 * Represents an Identity v2 Token.
 */
class Token extends OperatorResource implements \OpenStack\Common\Auth\Token
{
    /** @var \DateTimeImmutable */
    public $issuedAt;

    /** @var string */
    public $id;

    /** @var \DateTimeImmutable */
    public $expires;

    /** @var Tenant */
    public $tenant;

    /**
     * {@inheritdoc}
     */
    protected function getAliases()
    {
        return parent::getAliases() + [
            'tenant'    => new Alias('tenant', Tenant::class),
            'expires'   => new Alias('expires', \DateTimeImmutable::class),
            'issued_at' => new Alias('issuedAt', \DateTimeImmutable::class),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function populateFromResponse(ResponseInterface $response)
    {
        $this->populateFromArray(Utils::jsonDecode($response)['access']['token']);

        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function hasExpired()
    {
        return $this->expires <= new \DateTimeImmutable('now', $this->expires->getTimezone());
    }
}
