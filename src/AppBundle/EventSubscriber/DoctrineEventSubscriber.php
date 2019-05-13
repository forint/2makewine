<?php

namespace AppBundle\EventSubscriber;

use Doctrine\ORM\Events;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\OnFlushEventArgs;
use AppBundle\Annotation\Xss\AllowedTags;

class DoctrineEventSubscriber implements EventSubscriber
{

    /**
     * @var array
     */
    CONST ALLOWED_HTML_TAGS = [];

    public function getSubscribedEvents()
    {
        return array(
            Events::onFlush,
        );
    }
    /**
     * @param OnFlushEventArgs $args
     */
    public function onFlush(OnFlushEventArgs $args) {
        foreach ($args->getEntityManager()->getUnitOfWork()->getScheduledEntityUpdates() AS $entity) {
            $this->checkEntityForXss($args->getEntityManager(), $entity);
        }
        foreach ($args->getEntityManager()->getUnitOfWork()->getScheduledEntityInsertions() AS $entity) {
            $this->checkEntityForXss($args->getEntityManager(), $entity);
        }
    }
    /**
     * @param EntityManager $em
     * @param mixed $entity
     */
    private function checkEntityForXss(EntityManager $em, $entity) {
        $annotationReader = new AnnotationReader();
        $entityClass = ClassUtils::getClass($entity);
        $fieldMappings = $em->getClassMetadata($entityClass)->fieldMappings;
        foreach ($em->getUnitOfWork()->getEntityChangeSet($entity) AS $propertyName => $propertyChangeSet) {
            if (isset($fieldMappings[$propertyName]['type']) && in_array($fieldMappings[$propertyName]['type'], array('string', 'text'))) {
                $reflectionProperty = new \ReflectionProperty(isset($fieldMappings[$propertyName]['inherited']) ? $fieldMappings[$propertyName]['inherited'] : $entityClass, $propertyName);
                /** @var AllowedTags $allowedTagsAnnotation */
                $allowedTagsAnnotation = $annotationReader->getPropertyAnnotation(
                    $reflectionProperty,
                    AllowedTags::class
                );
                $allowedTags = $allowedTagsAnnotation ? $allowedTagsAnnotation->getTagNames() : self::ALLOWED_HTML_TAGS;
                $propertyGetter = "get" . ucfirst($propertyName);
                $propertySetter = "set" . ucfirst($propertyName);
                if (method_exists($entity, $propertySetter) && method_exists($entity, $propertyGetter) && $entity->{$propertyGetter}() && !is_array($entity->{$propertyGetter}())) {
                    // dump($entity->{$propertyGetter}());
                    $entity->{$propertySetter}(strip_tags($entity->{$propertyGetter}(), implode("", $allowedTags)));
                }
            }
        }
    }
}