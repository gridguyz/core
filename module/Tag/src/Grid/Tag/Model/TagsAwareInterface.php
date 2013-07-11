<?php

namespace Grid\Tag\Model;

/**
 * TagsAwareInterface
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
interface TagsAwareInterface
{

    /**
     * Get tags of the sructure
     *
     * @return array
     */
    public function getTags();

    /**
     * Get tag ids of the sructure
     *
     * @return array
     */
    public function getTagIds();

    /**
     * Get locale-tags of the sructure
     *
     * @return array
     */
    public function getLocaleTags();

}
