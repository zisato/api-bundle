<?php

declare(strict_types=1);

namespace Zisato\ApiBundle\Infrastructure\APIProblem;

final class APIProblem
{
    /**
     * @var int
     */
    public const STATUS_400 = 400;

    /**
     * @var int
     */
    public const STATUS_401 = 401;

    /**
     * @var int
     */
    public const STATUS_403 = 403;

    /**
     * @var int
     */
    public const STATUS_404 = 404;

    /**
     * @var int
     */
    public const STATUS_405 = 405;

    /**
     * @var int
     */
    public const STATUS_406 = 406;

    /**
     * @var int
     */
    public const STATUS_407 = 407;

    /**
     * @var int
     */
    public const STATUS_408 = 408;

    /**
     * @var int
     */
    public const STATUS_409 = 409;

    /**
     * @var int
     */
    public const STATUS_410 = 410;

    /**
     * @var int
     */
    public const STATUS_411 = 411;

    /**
     * @var int
     */
    public const STATUS_412 = 412;

    /**
     * @var int
     */
    public const STATUS_413 = 413;

    /**
     * @var int
     */
    public const STATUS_414 = 414;

    /**
     * @var int
     */
    public const STATUS_415 = 415;

    /**
     * @var int
     */
    public const STATUS_416 = 416;

    /**
     * @var int
     */
    public const STATUS_417 = 417;

    /**
     * @var int
     */
    public const STATUS_426 = 426;

    /**
     * @var int
     */
    public const STATUS_500 = 500;

    /**
     * @var int
     */
    public const STATUS_501 = 501;

    /**
     * @var int
     */
    public const STATUS_502 = 502;

    /**
     * @var int
     */
    public const STATUS_503 = 503;

    /**
     * @var int
     */
    public const STATUS_504 = 504;

    /**
     * @var int
     */
    public const STATUS_505 = 505;

    /**
     * @var array<int, string>
     */
    private const TITLES = [
        self::STATUS_400 => 'Bad Request',
        self::STATUS_401 => 'Unauthorized',
        self::STATUS_403 => 'Forbidden',
        self::STATUS_404 => 'Not Found',
        self::STATUS_405 => 'Method Not Allowed',
        self::STATUS_406 => 'Not Acceptable',
        self::STATUS_407 => 'Proxy Authentication Required',
        self::STATUS_408 => 'Request Timeout',
        self::STATUS_409 => 'Conflict',
        self::STATUS_410 => 'Gone',
        self::STATUS_411 => 'Length Required',
        self::STATUS_412 => 'Precondition Failed',
        self::STATUS_413 => 'Payload Too Large',
        self::STATUS_414 => 'URI Too Long',
        self::STATUS_415 => 'Unsupported Media Type',
        self::STATUS_416 => 'Range Not Satisfiable',
        self::STATUS_417 => 'Expectation Failed',
        self::STATUS_426 => 'Upgrade Required',
        self::STATUS_500 => 'Internal Server Error',
        self::STATUS_501 => 'Not Implemented',
        self::STATUS_502 => 'Bad Gateway',
        self::STATUS_503 => 'Service Unavailable',
        self::STATUS_504 => 'Gateway Timeout',
        self::STATUS_505 => 'HTTP Version Not Supported',
    ];

    /**
     * @var string
     */
    private const DEFAULT_TYPE = 'about:blank';

    /**
     * @var string
     */
    private const DEFAULT_TITLE = 'Unknown error';

    private readonly int $status;

    private readonly string $type;

    private readonly string $title;

    private readonly ?string $detail;

    private readonly ?string $instance;

    private readonly array $extensions;

    public function __construct(
        int $status,
        ?string $type = null,
        ?string $title = null,
        ?string $detail = null,
        ?string $instance = null,
        ?array $extensions = null
    ) {
        $this->status = $status;
        $this->type = $type ?? self::DEFAULT_TYPE;
        $this->title = self::TITLES[$status] ?? $title ?? self::DEFAULT_TITLE;
        $this->detail = $detail;
        $this->instance = $instance;
        $this->extensions = $extensions ?? [];
    }

    public function status(): int
    {
        return $this->status;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function detail(): ?string
    {
        return $this->detail;
    }

    public function instance(): ?string
    {
        return $this->instance;
    }

    public function extensions(): ?array
    {
        return $this->extensions;
    }

    /**
     * @return array<string, string|int|null>
     */
    public function toArray(): array
    {
        $data = [
            'status' => $this->status,
            'type' => $this->type,
            'title' => $this->title,
        ];

        if ($this->detail !== null) {
            $data['detail'] = $this->detail;
        }

        if ($this->instance !== null) {
            $data['instance'] = $this->instance;
        }

        return array_merge($data, $this->extensions);
    }
}
