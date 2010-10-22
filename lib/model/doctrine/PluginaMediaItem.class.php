<?php

/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
abstract class PluginaMediaItem extends BaseaMediaItem
{
  public function save(Doctrine_Connection $conn = null)
  {
    if (!$this->getOwnerId())
    {
      if (sfContext::hasInstance())
      {
        $user = sfContext::getInstance()->getUser();
        if ($user->getGuardUser())
        {
          $this->setOwnerId($user->getGuardUser()->getId());
        }
      }
    }
    // Let the culture be the user's culture
    $result = aZendSearch::saveInDoctrineAndLucene($this, null, $conn);
    $crops = $this->getCrops();
    foreach ($crops as $crop)
    {
      $crop->setTitle($this->getTitle());
      $crop->setDescription($this->getDescription());
      $crop->setCredit($this->getCredit());
      $crop->save();
    }
    return $result;
  }

  public function doctrineSave($conn)
  {
    $result = parent::save($conn);
    return $result;
  }

  public function delete(Doctrine_Connection $conn = null)
  {
    $ret = aZendSearch::deleteFromDoctrineAndLucene($this, null, $conn);
    $this->clearImageCache();
    
    $this->deleteCrops();
    
    // Don't even think about trashing the original until we know
    // it's gone from the db and so forth
    unlink($this->getOriginalPath());
    return $ret;
  }

  public function doctrineDelete($conn)
  {
    return parent::delete($conn);
  }
  
  public function updateLuceneIndex()
  {
    aZendSearch::updateLuceneIndex($this, array(
      'type' => $this->getType(),
      'title' => $this->getTitle(),
      'description' => $this->getDescription(),
      'credit' => $this->getCredit(),
      'categories' => implode(", ", $this->getCategoryNames()),
      'tags' => implode(", ", $this->getTags())
    ));
  }
  
  public function getCategoryNames()
  {
    $categories = $this->getCategories();
    $result = array();
    foreach ($categories as $category)
    {
      $result[] = $category->getName();
    }
    return $result;
  }
  
  public function getOriginalPath($format = false)
  {
    if ($format === false)
    {
      $format = $this->getFormat();
    }
    $path = aMediaItemTable::getDirectory() . 
      DIRECTORY_SEPARATOR . $this->getSlug() . ".original.$format";
    error_log("Media path will be $path");
    return $path;
  }
  
  public function clearImageCache($deleteOriginals = false)
  {
    if (!$this->getId())
    {
      return;
    }
    $cached = glob(aMediaItemTable::getDirectory() . DIRECTORY_SEPARATOR . $this->getSlug() . ".*");
    foreach ($cached as $file)
    {
      if (!$deleteOriginals)
      {
        if (strpos($file, ".original.") !== false)
        {
          continue;
        }
      }
      unlink($file); 
    }
  }
 
  // Now accepts either a file path (for backwards compatibility)
  // or an aValidatedFile object (better, supports more formats; see
  // the import-files task for how to exploit this outside of a form)
  public function preSaveFile($file)
  {
    if (get_class($file) === 'aValidatedFile')
    {
      $this->format = $file->getExtension();
      if (strlen($this->format))
      {
        // Starts with a .
        $this->format = substr($this->format, 1);
      }
      $types = aMediaTools::getOption('types');
      foreach ($types as $type => $info)
      {
        $extensions = $info['extensions'];
        if (in_array($this->format, $extensions))
        {
          $this->type = $type;
        }
      }
      $file = $file->getTempName();
    }
    // Refactored into aImageConverter for easier reuse of this should-be-in-PHP functionality
    $info = aImageConverter::getInfo($file);
    if ($info)
    {
      // Sometimes we store formats we can't get dimensions for on this particular platform
      if (isset($info['width']))
      {
        $this->width = $info['width'];
      }
      if (isset($info['height']))
      {
        $this->height = $info['height'];
      }
      // Don't force this, but it's useful when not invoked
      // with an aValidatedFile object
      if (is_null($this->format))
      {
        $this->format = $info['format'];
      }
      $this->clearImageCache(true);
    }
    // Always return true - we store a lot of files now, not just images
    return true;
  }

  // Now accepts either a file path (for backwards compatibility)
  // or an aValidatedFile object (better, supports more formats; see
  // the import-files task for how to exploit this outside of a form)
  public function saveFile($file)
  {
    if (!$this->width)
    {
      if (!$this->preSaveFile($file))
      {
        return false;
      }
    }
    if (get_class($file) === 'aValidatedFile')
    {
      $file = $file->getTempName();
    }
    
    $path = $this->getOriginalPath($this->getFormat());
    $result = copy($file, $path);
    // Crops are invalid if you replace the original image
    $this->deleteCrops();
    return $result;
  }

  public function getEmbedCode($width, $height, $resizeType, $format = 'jpg', $absolute = false, $wmode = 'opaque')
  {
    if ($height === false)
    {
      // Scale the height. I had this backwards
      $height = floor(($width * $this->height / $this->width) + 0.5); 
    }

    // Accessible alt title
    $title = htmlentities($this->getTitle(), ENT_COMPAT, 'UTF-8');
    if ($this->getEmbeddable())
    {
      if ($this->service_url)
      {
        $service = aMediaTools::getEmbedService($this->service_url);
        return $service->embed($service->getIdFromUrl($this->service_url), $width, $height, $title, $wmode);
      }
      elseif ($this->embed)
      {
        // Solution for non-YouTube videos based on a manually
        // provided thumbnail and embed code
        return str_replace(array('_TITLE_', '_WIDTH_', '_HEIGHT_'),
          array($title, $width, $height, $wmode), $this->embed);
      }
      else
      {
        throw new sfException('Media item without an embed code or a service url');
      }
    }
    elseif (($this->getType() == 'image') || ($this->getType() == 'pdf'))
    {
      // Use named routing rule to ensure the desired result (and for speed)
      return "<img alt=\"$title\" width=\"$width\" height=\"$height\" src='" . htmlspecialchars($this->getImgSrcUrl($width, $height, $resizeType, $format, $absolute)) . "' />";
    }
    else
    {
      throw new Exception("Unknown media type in getEmbedCode: " . $this->getType() . " id is " . $this->id . " is new? " . $this->isNew());
    }
  }
  
  // This is currently allowed for all types, although a PDF will give you a plain white box if you
  // don't have ghostscript available
  
  public function getImgSrcUrl($width, $height, $resizeType, $format = 'jpg', $absolute = false)
  {
    if ($height === false)
    {
      // Scale the height. I had this backwards
      $height = floor(($width * $this->height / $this->width) + 0.5); 
    }

    $controller = sfContext::getInstance()->getController();
    $slug = $this->getSlug();
    // Use named routing rule to ensure the desired result (and for speed)
    return $controller->genUrl("@a_media_image?" . 
      http_build_query(
        array("slug" => $slug, 
          "width" => $width, 
          "height" => $height, 
          "resizeType" => $resizeType,
          "format" => $format)), $absolute);
  }
  
  protected function youtubeUrlToEmbeddedUrl($url)
  {
    $url = str_replace("/watch?v=", "/v/", $url);
    $url .= "&fs=1";
    return $url;
  }
  public function userHasPrivilege($privilege, $user = false)
  {
    if ($user === false)
    {
      $user = sfContext::getInstance()->getUser();
    }
    if ($privilege === 'view')
    {
      if ($this->getViewIsSecure())
      {
        if (!$user->isAuthenticated())
        {
          return false;
        }
      }
      return true;
    }
    if ($user->hasCredential(aMediaTools::getOption('admin_credential')))
    {
      return true;
    }
    $guardUser = $user->getGuardUser();
    if (!$guardUser)
    {
      return false;
    }
    if ($this->getOwnerId() === $guardUser->getId())
    {
      return true;
    }
    return false;
  }
  
  // Returns a Symfony action URL. Call url_for or use sfController for final routing.
  
  public function getScaledUrl($options)
  {
    $options = aDimensions::constrain($this->getWidth(), $this->getHeight(), $this->getFormat(), $options);
    $params = array("slug" => $this->slug, "width" => $options['width'], "height" => $options['height'], 
      "resizeType" => $options['resizeType'], "format" => $options['format']);

    // check for null because 0 is valid
    if (!is_null($options['cropLeft']) && !is_null($options['cropTop']) && !is_null($options['cropWidth']) && !is_null($options['cropHeight']))
    {      
      $params = array_merge(
        $params,
        array("cropLeft" => $options['cropLeft'], "cropTop" => $options['cropTop'],
          "cropWidth" => $options['cropWidth'], "cropHeight" => $options['cropHeight'])
      );
    }
    return "aMediaBackend/image?" . http_build_query($params);
  }
  
  public function getCropThumbnailUrl()
  {    
    $selectedConstraints = aMediaTools::getOption('selected_constraints');
    
    if ($aspectRatio = aMediaTools::getAspectRatio()) // this returns 0 if aspect-width and aspect-height were not set
    {
      $selectedConstraints = array_merge(
        $selectedConstraints, 
        array('height' => floor($selectedConstraints['width'] / $aspectRatio))
      );
    }
    
    
    $imageInfo = aMediaTools::getAttribute('imageInfo');
    if (isset($imageInfo[$this->id]['cropLeft']) &&
        isset($imageInfo[$this->id]['cropTop']) && isset($imageInfo[$this->id]['cropWidth']) && isset($imageInfo[$this->id]['cropHeight']))
    {
      $selectedConstraints = array_merge(
        $selectedConstraints, 
        array(
          'cropLeft' => $imageInfo[$this->id]['cropLeft'],
          'cropTop' => $imageInfo[$this->id]['cropTop'],
          'cropWidth' => $imageInfo[$this->id]['cropWidth'],
          'cropHeight' => $imageInfo[$this->id]['cropHeight']
        )
      );
    }
      
    return $this->getScaledUrl($selectedConstraints);
  }
  
  // Crops of other images have periods in the slug. Real slugs are always [\w_]+ (well, the i18n equivalent)
  public function isCrop()
  {
    return (strpos($this->slug, '.') !== false);
  }
  
  public function getCrops()
  {
    // This should perform well because there is an index on the slug and
    // indexes are great with prefix queries
    return $this->getTable()->createQuery('m')->where('m.slug LIKE ?', array($this->slug . '.%'))->execute();
    
  }
  
  public function deleteCrops()
  {
    $crops = $this->getCrops();
    // Let's make darn sure the PHP stuff gets called rather than using a delete all trick of some sort
    foreach ($crops as $crop)
    {
      $crop->delete();
    }
  }
  
  public function findOrCreateCrop($info)
  {
    $slug = $this->slug . '.' . $info['cropLeft'] . '.' . $info['cropTop'] . '.' . $info['cropWidth'] . '.' . $info['cropHeight'];
    $crop = $this->getTable()->findOneBySlug($slug);
    if (!$crop)
    {
      $crop = $this->copy(false);
      $crop->slug = $slug;
      $crop->width = $info['cropWidth'];
      $crop->height = $info['cropHeight'];
    }
    return $crop;
  }
  
  public function getCroppingInfo()
  {
    $p = preg_split('/\./', $this->slug);
    if (count($p) == 5)
    {
      // Without the casts JSON won't give integers to JavaScript see #640
      return array('cropLeft' => (int) $p[1], 'cropTop' => (int) $p[2], 'cropWidth' => (int) $p[3], 'cropHeight' => (int) $p[4]);
    }
    else
    {
      return array();
    }
  }
  
  public function getCropOriginal()
  {
    if (!$this->isCrop())
    {
      return $this;
    }
    $p = preg_split('/\./', $this->slug);
    return $this->getTable()->findOneBySlug($p[0]);
  }
  
  public function getDownloadable()
  {
    $type = aMediaTools::getTypeInfo($this->type);
    return $type['downloadable'];
  }
  
  public function getEmbeddable()
  {
    $type = aMediaTools::getTypeInfo($this->type);
    return $type['embeddable'];
  }
  
  public function getImageAvailable()
  {
    // We don't display thumbnails for non-native embeds anymore,
    // and for new adds of non-native embeds we don't have them at all
    return $this->width && (!strlen($this->embed));
  }
  
  public function getCroppable()
  {
    // Right now images are always croppable and nothing else is
    return ($this->type === 'image');
  }
}
