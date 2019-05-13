<?php

namespace AppBundle\Annotation\Xss;

use Symfony\Component\Serializer\Exception\InvalidArgumentException;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 */
class AllowedTags
{
    /**
     * @var array
     */
    private $tagNames = [];

    public function __construct(array $data)
    {
        if (!isset($data['tagNames']) || !is_array($data['tagNames']) || empty($data['tagNames'])) {
            throw new InvalidArgumentException(
                sprintf('Parameter tagNames of annotation "%s" cannot be empty.',
                    get_class($this))
            );
        }
        $tagNamePattern = '/^<[a-z]+>$/';
        foreach ($data['tagNames'] AS $tagName)
        {
            if (1 !== preg_match($tagNamePattern, $tagName))
            {
                throw new InvalidArgumentException(
                    sprintf('TagName "%s" of annotation "%s" didnt match the pattern "%s".',
                        $tagName,
                        get_class($this),
                        $tagNamePattern
                    )
                );
            }
        }
        $this->tagNames = $data['tagNames'];
    }

    /**
     * @return array
     */
    public function getTagNames()
    {
        return $this->tagNames;
    }
}