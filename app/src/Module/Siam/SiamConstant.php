<?php

declare(strict_types = 1);

namespace App\Module\Siam;

final class SiamConstant
{
    public const ROUTE_CHECK = 'api_siam_check';

    public const SSO_CLIENT_NAME = 'siam_client';
    public const SSO_SCOPE = 'sso';
    public const SSO_RESOURCE_SERVER = 'my_resource_server';

    public const RESPONSE_KEY_WORKFORCE_ID = 'workforceId';
    public const RESPONSE_KEY_ROLES = 'groups';
}
