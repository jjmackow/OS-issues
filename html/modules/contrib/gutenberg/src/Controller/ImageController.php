<?php

namespace Drupal\gutenberg\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\editor\Entity\Editor;
use Drupal\file\Entity\File;

/**
 * Returns responses for our image routes.
 */
class ImageController extends ControllerBase {

  /**
   * Returns JSON representing the new file upload, or validation errors.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request.
   * @param \Drupal\editor\Entity\Editor $editor
   *   The editor.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   The JSON response.
   */
  public function upload(Request $request, Editor $editor = NULL) {
    $imageSettings = $editor->getImageUploadSettings();

    $filename = $_FILES['files']['name']['fid'];
    $directory = "{$imageSettings['scheme']}://{$imageSettings['directory']}";
    $data = file_get_contents($_FILES['files']['tmp_name']['fid']);

    // TODO: File size and image dimensions validations.
    //      Better error handling?
    if (file_prepare_directory($directory, FILE_CREATE_DIRECTORY)) {
      $file = file_save_data($data, "{$directory}/{$filename}", FILE_EXISTS_RENAME);
      // $file->setTemporary();
      $file->setPermanent();
      $file->save();
    }
    else {
      return new JsonResponse(NULL, 500);
    }

    $image_src = file_create_url($file->getFileUri());
    return new JsonResponse([
      'id' => (int) $file->id(),
      'source_url' => $image_src,
      'link' => $image_src,
      'media_type' => explode('/', $file->getMimeType())[0],
      'title' => [
        'raw' => ''
      ],
      'alt_text' => '',
      'data' => [
        'entity_type' => 'file',
        'entity_uuid' => $file->uuid(),
      ],
    ]);
  }

  /**
   * Returns JSON representing the loaded file.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request.
   * @param \Drupal\editor\Entity\Editor $editor
   *   The editor.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   The JSON response.
   */
  public function load(Request $request, File $file = NULL) {
    $image_src = file_create_url($file->getFileUri());

    return new JsonResponse([
      'id' => $file->id(),
      'source_url' => $image_src,
      'link' => $image_src,
      'media_type' => 'image',
      'title' => [
        'raw' => ''
      ],
      'alt_text' => '',
      'data' => [
        'entity_type' => 'file',
        'entity_uuid' => $file->uuid(),
      ],
    ]);
  }

}
