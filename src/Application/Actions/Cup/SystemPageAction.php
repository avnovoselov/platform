<?php declare(strict_types=1);

namespace App\Application\Actions\Cup;

use App\Domain\AbstractAction;
use App\Domain\Entities\User;
use App\Domain\Service\Catalog\MeasureService as CatalogMeasureService;
use App\Domain\Service\Catalog\OrderStatusService as CatalogOrderStatusService;
use App\Domain\Service\Parameter\ParameterService;
use App\Domain\Service\User\Exception\TitleAlreadyExistsException;
use App\Domain\Service\User\GroupService as UserGroupService;
use App\Domain\Service\User\UserService;

class SystemPageAction extends AbstractAction
{
    private const LOCK_FILE = VAR_DIR . '/installer.lock';

    private const PRIVATE_SECRET_FILE = VAR_DIR . '/private.secret.key';

    private const PUBLIC_SECRET_FILE = VAR_DIR . '/public.secret.key';

    protected function action(): \Slim\Psr7\Response
    {
        $access = false;

        // first install
        if (!file_exists(self::LOCK_FILE)) {
            $access = true;
        }

        /** @var false|User $user */
        $user = $this->request->getAttribute('user', false);

        // exist user
        if (!$access && $user) {
            if ($user->getGroup() !== null && in_array('cup:main', $user->getGroup()->getAccess(), true)) {
                $access = true;
            }
        }

        // ok, allow access to page
        if ($access) {
            if ($this->isPost()) {
                $this->setup_database();
                $this->setup_user();
                $this->setup_data();
                $this->gen_openssl();

                // write lock file
                file_put_contents(self::LOCK_FILE, time());

                return $this->respondWithRedirect('/cup');
            }

            return $this->respondWithTemplate('cup/system/index.twig', [
                'step' => $this->args['step'] ?? '1',
                'health' => $this->self_check(),
            ]);
        }

        return $this->respondWithRedirect('/cup/login?redirect=/cup/system');
    }

    protected function setup_database(): void
    {
        if ($databaseAction = $this->getParam('database', '')) {
            $schema = new \Doctrine\ORM\Tools\SchemaTool($this->entityManager);

            switch ($databaseAction) {
                case 'create':
                    $schema->createSchema($this->entityManager->getMetadataFactory()->getAllMetadata());

                    break;

                case 'update':
                    $schema->updateSchema($this->entityManager->getMetadataFactory()->getAllMetadata());

                    break;

                case 'delete':
                    $schema->dropSchema($this->entityManager->getMetadataFactory()->getAllMetadata());

                    break;
            }
        }
    }

    protected function setup_user(): void
    {
        if ($userData = $this->getParam('user', [])) {
            $userGroupService = $this->container->get(UserGroupService::class);
            $userService = $this->container->get(UserService::class);

            // create or read group
            try {
                $userData['group'] = $userGroupService->create([
                    'title' => __('Administrators'),
                    'access' => $this->getRoutes()->values()->all(),
                ]);
            } catch (TitleAlreadyExistsException) {
                $userData['group'] = $userGroupService->read([
                    'title' => __('Administrators'),
                ]);
            }

            // create user with administrator group
            $userService->create($userData);
        }
    }

    protected function setup_data(): void
    {
        if ('system_default' === $this->getParam('fill', 'no')) {
            $order_status = [
                ['title' => __('New'), 'order' => 1],
                ['title' => __('In processing'), 'order' => 2],
                ['title' => __('Sent'), 'order' => 3],
                ['title' => __('Delivered'), 'order' => 4],
                ['title' => __('Canceled'), 'order' => 5],
            ];
            foreach ($order_status as $el) {
                $this->container->get(CatalogOrderStatusService::class)->create($el);
            }

            $product_measure = [
                ['title' => __('Kilogram'), 'contraction' => __('kg'), 'value' => 1000],
                ['title' => __('Gram'), 'contraction' => __('g'), 'value' => 1],
                ['title' => __('Liter'), 'contraction' => __('l'), 'value' => 1000],
                ['title' => __('Milliliter'), 'contraction' => __('ml'), 'value' => 1],
            ];
            foreach ($product_measure as $el) {
                $this->container->get(CatalogMeasureService::class)->create($el);
            }

            $this->container->get(ParameterService::class)->create([
                'key' => 'catalog_invoice',
                'value' => INVOICE_TEMPLATE,
            ]);
        }
    }

    protected function gen_openssl(): void
    {
        if (!file_exists(self::PRIVATE_SECRET_FILE) || !file_exists(self::PUBLIC_SECRET_FILE)) {
            // generate private key file
            $privateKeyResource = openssl_pkey_new([
                'private_key_bits' => 2048,
                'private_key_type' => OPENSSL_KEYTYPE_RSA,
            ]);

            openssl_pkey_export_to_file($privateKeyResource, self::PRIVATE_SECRET_FILE);

            // generate public key for private key
            $privateKeyDetailsArray = openssl_pkey_get_details($privateKeyResource);

            file_put_contents(self::PUBLIC_SECRET_FILE, $privateKeyDetailsArray['key']);
        }
    }

    protected function self_check(): array
    {
        $fileAccess = [
            BASE_DIR => 0o755,
            BIN_DIR => 0o755,
            CONFIG_DIR => 0o755,
            PLUGIN_DIR => 0o777,
            PUBLIC_DIR => 0o755,
            RESOURCE_DIR => 0o755,
            UPLOAD_DIR => 0o777,
            SRC_DIR => 0o755,
            SRC_LOCALE_DIR => 0o755,
            VIEW_DIR => 0o755,
            VIEW_ERROR_DIR => 0o755,
            THEME_DIR => 0o777,
            VAR_DIR => 0o777,
            CACHE_DIR => 0o777,
            LOG_DIR => 0o777,
            VENDOR_DIR => 0o755,
        ];

        foreach ($fileAccess as $folder => $value) {
            if (realpath($folder)) {
                if ($value === (@fileperms($folder) & 0o777) || @chmod($folder, $value)) {
                    $fileAccess[$folder] = true;
                } else {
                    $fileAccess[$folder] = decoct($value);
                }
            }
        }

        return [
            'php' => version_compare(phpversion(), '8.1', '>='),
            'extensions' => [
                'pdo' => extension_loaded('pdo'),
                // 'pdo_mysql' => extension_loaded('pdo_mysql'),
                // 'pdo_pgsql' => extension_loaded('pdo_pgsql'),
                // 'sqlite3' => extension_loaded('sqlite3'),
                'curl' => extension_loaded('curl'),
                'json' => extension_loaded('json'),
                'mbstring' => extension_loaded('mbstring'),
                'gd' => extension_loaded('gd'),
                'imagick' => extension_loaded('imagick'),
                'xml' => extension_loaded('xml'),
                'yaml' => extension_loaded('yaml'),
                'zip' => extension_loaded('zip'),
            ],
            'folders' => $fileAccess,
        ];
    }
}

const INVOICE_TEMPLATE = <<<'EOD'
    <div class="m-5">
        <div class="row">
            <div class="col-12 text-center">
                <h3 class="font-weight-bold">{{ 'Invoice'|locale }}</h3>
                {#<img src="/images/logo.png" style="width: 100%; max-width: 300px" />#}
            </div>
            <div class="col-6">
                {{ parameter('common_title') }}<br />
                {{ 'Order'|locale }}: <b>{{ order.external_id ?: order.serial }}</b><br />
                {{ 'Date'|locale }}: <b>{{ order.date|df('d.m.Y H:i') }}</b><br />
                {{ 'Shipping'|locale }}: <b>{{ order.shipping|df('d.m.Y H:i') }}</b>
            </div>
            <div class="col-6 text-right">
                {{ qr_code(base_url() ~ '/cart/done/' ~ order.uuid, 100) }}
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-6">
                {{ order.delivery.client }}<br />
                {{ order.phone ? order.phone : '-' }}<br />
                {{ order.email ? order.email : '-' }}
            </div>
            <div class="col-6 text-right">
                {{ order.delivery.address }}<br />
                {{ order.comment }}
            </div>
        </div>

        <div class="row py-1 mt-3 bg-grey2">
            <div class="col-8 col-md-6 text-nowrap font-weight-bold">{{ 'Item'|locale }}</div>
            <div class="d-none d-md-block col-md-2 text-right text-nowrap font-weight-bold">{{ 'Price'|locale }}</div>
            <div class="d-none d-md-block col-md-2 text-right text-nowrap font-weight-bold">{{ 'Quantity'|locale }}</div>
            <div class="col-4 col-md-2 text-right text-nowrap font-weight-bold">{{ 'Sum'|locale }}</div>
        </div>

        {% set total = 0 %}
        {% for item in order.products %}
            <div class="row py-1 {{ loop.last ?: 'border-bottom' }} {{ loop.index0 % 2 ? 'bg-grey1' }}">
                <div class="col-8 col-md-6 overflow-hidden text-nowrap">{{ item.title }}</div>
                <div class="d-none d-md-block col-md-2 text-right text-nowrap">{{ item.price|number_format(2, '.', ' ') }}</div>
                <div class="d-none d-md-block col-md-2 text-right text-nowrap">{{ item.count }}</div>
                <div class="col-4 col-md-2 text-right text-nowrap">{{ (item.price * item.count)|number_format(2, '.', ' ') }}</div>
            </div>
        {% endfor %}

        <div class="row py-1">
            <div class="col-12 text-right text-nowrap font-weight-bold border-top">{{ 'Total price'|locale }}: {{ order.getTotalPrice()|number_format(2, '.', ' ') }}</div>
        </div>
    </div>
    EOD;
