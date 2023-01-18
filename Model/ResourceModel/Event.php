<?php

namespace Elisa\ProductApi\Model\ResourceModel;

use Elisa\ProductApi\Model\Event as Model;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Serialize\Serializer\Json;

/**
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
class Event extends AbstractDb
{
    private const UTF8_MB4_FIELDS = [Model::KEY_DESCRIPTION, Model::KEY_NAME_SHORT, Model::KEY_NAME];

    /** @var bool */
    protected $_isPkAutoIncrement = false;
    /** @var Json */
    protected $jsonSerializer;

    /**
     * @param Json $jsonSerializer
     * @param Context $context
     * @param string|null $connectionName
     */
    public function __construct(Json $jsonSerializer, Context $context, $connectionName = null)
    {
        parent::__construct($context, $connectionName);
        $this->jsonSerializer = $jsonSerializer;
    }

    /**
     * @inheritDoc
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    protected function _construct()
    {
        $this->_init('elisa_productsapi_event', Model::KEY_EVENT_ID);
    }

    /**
     * Delete all events whose ID is not in provided list
     *
     * @param string[] $currentEventIds
     * @return string[] $deletedEventIds
     * @throws LocalizedException
     */
    public function getStaleEventIds(array $currentEventIds): array
    {
        $select = $this->getConnection()->select();
        $table = $this->getMainTable();
        $select->from($table, Model::KEY_EVENT_ID);

        if ($currentEventIds) {
            $select->where(Model::KEY_EVENT_ID . ' NOT IN (?)', $currentEventIds);
        }

        return $this->getConnection()->fetchCol($select);
    }

    /**
     * Return available tags for cached events
     *
     * @return array
     * @throws LocalizedException
     */
    public function getTags(): array
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getMainTable(), Model::KEY_TAGS);
        $tagCsvRows = $connection->fetchCol($select);

        $tags = array_reduce(
            $tagCsvRows,
            function ($carry, $tagCsv) {
                return array_merge($carry, explode(',', $tagCsv ?? ''));
            },
            []
        );

        return array_unique(array_filter($tags));
    }

    /**
     * @inheritDoc
     */
    public function unserializeFields(\Magento\Framework\Model\AbstractModel $object)
    {
        parent::unserializeFields($object);

        if ($object->hasData(Model::KEY_TAGS)) {
            $tags = $object->getData(Model::KEY_TAGS);

            if (is_string($tags)) {
                $object->setData(Model::KEY_TAGS, explode(',', $tags));
            }
        }

        foreach (self::UTF8_MB4_FIELDS as $field) {
            if ($object->hasData($field)) {
                $fieldValue = $object->getData($field);
                $object->setData($field, $this->jsonSerializer->unserialize($fieldValue)['data']);
            }
        }

        return $object;
    }

    /**
     * @inheritDoc
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    protected function _serializeFields(\Magento\Framework\Model\AbstractModel $object)
    {
        parent::_serializeFields($object);

        if ($object->hasData(Model::KEY_TAGS)) {
            $tags = $object->getData(Model::KEY_TAGS);

            if (is_array($tags)) {
                $object->setData(Model::KEY_TAGS, implode(',', $tags));
            }
        }

        foreach (self::UTF8_MB4_FIELDS as $field) {
            if ($object->hasData($field)) {
                $fieldValue = $object->getData($field);
                $object->setData($field, $this->jsonSerializer->serialize(['data' => $fieldValue]));
            }
        }
    }
}
