<?php

namespace Charcoal\Notification\Transformer;

use Charcoal\Admin\Support\BaseUrlTrait;
use Charcoal\Admin\User;
use Charcoal\Model\ModelFactoryTrait;
use Charcoal\Notification\Object\NotificationTarget;

class NotificationTransformer
{
    use ModelFactoryTrait;
    use BaseUrlTrait;

    /**
     * Transformer constructor.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->setBaseUrl($data['base-url']);
        $this->setModelFactory($data['model/factory']);
    }

    /**
     * @param ModelInterface|mixed $model The model to transform.
     * @return array
     * @link http://php.net/manual/en/language.oop5.magic.php#language.oop5.magic.invoke
     */
    public function __invoke($model)
    {
        $baseUrl           = $this->baseUrl();
        $factory           = $this->modelFactory();
        $diff              = $model->dataDiff();
        $updatedProperties = isset($diff[0]) ? array_keys($diff[0]) : [];
        $targetObject      = $this->modelFactory()->create($model->targetType())->load($model->targetId());
        return [
            'id',
            'frequency',
            'targetId',
            'extraEmails',
            'updatedProperties' => function ($model) use ($updatedProperties) {
                return $updatedProperties;
            },
            'numProperties'     => function ($model) use ($updatedProperties) {
                return count($updatedProperties);
            },
            'propertiesString'  => function ($model) use ($updatedProperties) {
                return implode(', ', $updatedProperties);
            },
            'dateStr'           => function ($model) {
                return $model->revTs()->format('Y-m-d H:i:s');
            },
            'title'             => function ($model) use ($targetObject) {
                if (is_callable([$targetObject, 'title'])) {
                    return $targetObject->title();
                }

                if (is_callable([$targetObject, 'name'])) {
                    return $targetObject->name();
                }

                return '';
            },
            'targetTypeLabel'   => function ($model) use ($targetObject, $factory) {
                $target = $factory->create(NotificationTarget::class)->load($model->targetType());
                if ($target->id()) {
                    return (string)$target->label();
                }

                if (isset($targetObject->metadata()['label'])) {
                    return $this->translator()->translation($targetObject->metadata()['label']);
                }

                return $model->targetType();
            },
            'userObject'        => function ($model) use ($factory) {
                $user = $factory->create(User::class)->load($model->revUser());
                return [
                    'id' => $user->id(),
                    'email' => $user->email(),
                    'displayName' => $user->displayName()
                ];
            },
            'publicUrl'         => function ($model) use ($targetObject, $baseUrl) {
                return is_callable([$targetObject, 'url']) ? $baseUrl . $targetObject->url() : null;
            },
            'charcoalUrl'       => function ($model) use ($baseUrl) {
                return sprintf(
                    $baseUrl . 'admin/object/edit?obj_type=%s&obj_id=%s',
                    $model->targetType(),
                    $model->targetId()
                );
            }

        ];
    }
}
