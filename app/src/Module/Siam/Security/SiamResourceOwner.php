<?php

declare(strict_types = 1);

namespace App\Module\Siam\Security;

use App\Module\Siam\SiamConstant;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;

final class SiamResourceOwner implements ResourceOwnerInterface
{
    private $responseData;

    /**
     * @param string[] $responseData
     */
    public function __construct(array $responseData = [])
    {
        $this->responseData = $responseData;
    }

    public function getId(): ?string
    {
        return $this->responseData[SiamConstant::RESPONSE_KEY_WORKFORCE_ID] ?? null;
    }

    /**
     * @return array<string>
     */
    public function toArray(): array
    {
        return $this->responseData;
    }

    /**
     * @return string[]
     */
    public function getRoles(): array
    {
        return $this->responseData[SiamConstant::RESPONSE_KEY_ROLES] ?? [];
    }
}
