<?php

declare(strict_types=1);

namespace Drupal\corplite_published_releases_feed;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;

/**
 * Provides an interface defining a published releases entity type.
 */
interface PublishedReleasesInterface extends ContentEntityInterface, EntityChangedInterface {

}
