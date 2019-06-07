<?php

namespace App\Action\ResponseCreator\ResponseSerializer;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Component\Serializer\Serializer;

class JsonSerializer
{
    private const SERIALIZE_FORMAT = 'json';
    private const DATE_FORMAT = 'Y-m-d';

    /** @var Serializer */
    private $serializer;

    public function __construct()
    {
        $normalizers = [
            new PropertyNormalizer(),
            new DateTimeNormalizer([DateTimeNormalizer::FORMAT_KEY => self::DATE_FORMAT]),
            new ObjectNormalizer(),
        ];
        $encoders = [new JsonEncoder()];

        $this->serializer = new Serializer($normalizers, $encoders);
    }

    public function serialize(object $entity): string
    {
        $serializedEntity = $this->serializer->serialize($entity, self::SERIALIZE_FORMAT);

        return $serializedEntity;
    }
}