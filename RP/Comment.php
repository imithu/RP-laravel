<?php
namespace RP;

use Illuminate\Support\Facades\DB;


class Comment
{


  /**
   * add comment
   * 
   * @param string $base_key
   * @param int    $base_id
   * @param int    $id_user_to
   * @param int    $id_user_from
   * @param string $content_subject
   * @param string $content_body
   * 
   * @return int   $id
   * 
   * @since   1.0.0
   * @version 1.3.0
   * @author  Mahmudul Hasan Mithu
   */
  public static function add( string $base_key='comment', int $base_id, int $id_user_to=0, int $id_user_from=0, string $content_subject='', string $content_body='' ): int
  {
    $base_key        = htmlspecialchars(trim($base_key));
    $content_subject = htmlspecialchars(trim($content_subject));
    $content_body    = htmlspecialchars(trim($content_body));

    return DB::table('RP_comment')->insertGetId([
      'base_key'=> $base_key,
      'base_id' => $base_id,
      'id_user_to' => $id_user_to,
      'id_user_from' => $id_user_from,
      'content_subject' => $content_subject,
      'content_body' => $content_body,
      'edited' => 0,
      'status' => 'publish',
      'datetime' => \Misc\Moment::datetime()
    ]);
  }


  /**
   * update subject and body of a comment
   * 
   * @param int    $id
   * @param string $content_subject
   * @param string $content_body
   * 
   * @return void
   * 
   * @since   1.0.0
   * @version 1.0.0
   * @author  Mahmudul Hasan Mithu
   */
  public static function update(int $id, string $content_subject='', string $content_body=''): void
  {
    $content_subject = htmlspecialchars(trim($content_subject));
    $content_body    = htmlspecialchars(trim($content_body));

    DB::table('RP_comment')->where('id', $id)->increment('edited', 1, [
      'content_subject' => $content_subject,
      'content_body' => $content_body
    ]);
  }


  /**
   * delete comment
   * 
   * @param int $id
   * @param int $id_user - if id_user>0 then check id_user condition
   * 
   * @return void
   * 
   * @since   1.0.0
   * @version 1.3.0
   * @author  Mahmudul Hasan Mithu
   */
  public static function delete(int $id, int $id_user=0): void
  {
    if($id_user>0){
      // delete 1st level comment
      $success = DB::table('RP_comment')->where('id', $id)->where('id_user_from', $id_user)->delete();
      // delete 2nd level comment
      if($success) DB::table('RP_comment')->where('base_key', 'comment')->where('base_id', $id)->delete();
    }elseif($id_user<1) {
      // delete 1st level comment
      DB::table('RP_comment')->where('id', $id)->delete();
      // delete 2nd level comment
      DB::table('RP_comment')->where('base_key', 'comment')->where('base_id', $id)->delete();
    }
  }


}
