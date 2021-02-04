<?php
namespace App\Helpers;
use Intervention\Image\Filters\FilterInterface;

class ImageFilter implements FilterInterface
{
  /**
   * Default size of filter effects
   */
  const DEFAULT_SIZE = 10;

  /**
   * Size of filter effects
   *
   * @var integer
   */
  private $size;
  public $image;
  /**
   * Creates new instance of filter
   *
   * @param integer $size
   */
  public function __construct($size = null)
  {
    $this->size = is_numeric($size) ? intval($size) : self::DEFAULT_SIZE;
  }

  /**
   * @param \Intervention\Image\Image $image
   *
   * @return \Intervention\Image\Image|string
   */
  public function applyFilter(\Intervention\Image\Image $image)
  {
    $wx_logo_im = $image->getCore();
    $w = imagesx($wx_logo_im);
    $h = imagesy($wx_logo_im);
    $img = imagecreatetruecolor($w, $h);
    imagesavealpha($img, true);
    $bg = imagecolorallocatealpha($img, 255, 255, 255, 127);
    imagefill($img, 0, 0, $bg);
    $r   = $w /2; //圆半径
    for ($x = 0; $x < $w; $x++) {
      for ($y = 0; $y < $h; $y++) {
        $rgbColor = imagecolorat($wx_logo_im, $x, $y);
        if (((($x - $r) * ($x - $r) + ($y - $r) * ($y - $r)) < ($r * $r))) {
          imagesetpixel($img, $x, $y, $rgbColor);
        }
      }
    }
    return $img;
  }
}
