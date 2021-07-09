<?php
namespace Misc\Media;




class Stream
{


  private static $path = "";
  private static $stream = "";
  private static $buffer = 102400; // bytes
  private static $start  = -1;
  private static $end    = -1;
  private static $size   = 0;




  private static function setHeader()
  {
    ob_get_clean();
    header("Content-Type: ".mime_content_type(self::$path));
    header("Cache-Control: max-age=86400, public");
    header("Expires: ".gmdate('D, d M Y H:i:s', time()+86400) . ' GMT');
    header("Last-Modified: ".gmdate('D, d M Y H:i:s', @filemtime(self::$path)) . ' GMT' );
    self::$start = 0;
    self::$size  = filesize(self::$path);
    self::$end   = self::$size - 1;
    header("Accept-Ranges: 0-".self::$end);
      
    if (isset($_SERVER['HTTP_RANGE'])) {
      $c_start = self::$start;
      $c_end = self::$end;

      list(, $range) = explode('=', $_SERVER['HTTP_RANGE'], 2);
      if (strpos($range, ',') !== false) {
        header('HTTP/1.1 416 Requested Range Not Satisfiable');
        header('Content-Range: bytes '.self::$start.'-'.self::$end.'/'.self::$size);
        exit;
      }
      if ($range == '-') {
        $c_start = self::$size - substr($range, 1);
      }else{
        $range = explode('-', $range);
        $c_start = $range[0];
          
        $c_end = (isset($range[1]) && is_numeric($range[1])) ? $range[1] : $c_end;
      }
      $c_end = ($c_end > self::$end) ? self::$end : $c_end;
      if ($c_start > $c_end || $c_start > self::$size - 1 || $c_end >= self::$size) {
        header('HTTP/1.1 416 Requested Range Not Satisfiable');
        header('Content-Range: bytes '.self::$start.'-'.self::$end.'/'.self::$size);
        exit;
      }
      self::$start = $c_start;
      self::$end = $c_end;
      $length = self::$end - self::$start + 1;
      fseek(self::$stream, self::$start);
      header('HTTP/1.1 206 Partial Content');
      header("Content-Length: ".$length);
      header('Content-Range: bytes '.self::$start.'-'.self::$end.'/'.self::$size);
    }
    else
    {
      header("Content-Length: ".self::$size);
    }
  }




  private static function end()
  {
    fclose(self::$stream);
    exit;
  }




  private static function stream()
  {
    $i = self::$start;
    set_time_limit(0);
    while(!feof(self::$stream) && $i <= self::$end) {
      $bytesToRead = self::$buffer;
      if(($i+$bytesToRead) > self::$end) {
        $bytesToRead = self::$end - $i + 1;
      }
      $data = fread(self::$stream, $bytesToRead);
      echo $data;
      flush();
      $i += $bytesToRead;
    }
  }




  /**
   * @param file - $path - path of the file
   * 
   * @since   1.1.0
   * @version 1.1.0
   * @author  Mahmudul Hasan Mithu
   */
  public static function start($path)
  {
    self::$path = $path;
    self::$stream = fopen($path, 'rb');
    self::setHeader();
    self::stream();
    self::end();
  }


}
