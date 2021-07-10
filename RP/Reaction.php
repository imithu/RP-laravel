<?php
namespace RP;

use Illuminate\Support\Facades\DB;


class Reaction
{


  /**
   * add or update reaction
   * 
   * @param string $base_key
   * @param int    $base_id
   * @param int    $id_user
   * @param string $value     // default is '' .  eg. like | unlike
   * 
   * @return void
   * 
   * @since   1.0.0
   * @version 1.0.0
   * @author  Mahmudul Hasan Mithu
   */
  public static function set( string $base_key='comment', int $base_id, int $id_user=0, string $value=''): void{
    $base_key = htmlspecialchars(trim($base_key));
    $value    = htmlspecialchars(trim($value));

    DB::table('RP_reaction')->updateOrInsert(
      [
        'base_key'=> $base_key,
        'base_id' => $base_id,
        'id_user' => $id_user,
      ],
      [
        'value' => $value,
        'status' => 'publish',
        'datetime' => \Misc\Moment::datetime()
      ]
    );
  }


  /**
   * get reaction value
   * 
   * @param int    $id_user
   * @param string $base_key
   * @param int    $base_id
   * 
   * @return null|string NULL   - not found
   *                     string - reaction value
   * 
   * @since   1.1.0
   * @version 1.1.0
   * @author  Mahmudul Hasan Mithu
   */
  public static function get_value( int $id_user, string $base_key, int $base_id ){
    $value = DB::table('RP_reaction')->where('id_user', $id_user)->where('base_key', $base_key)->where('base_id', $base_id)->value('value');
    if($value!==NULL) return htmlspecialchars_decode($value);
    else return NULL;
  }


  /**
   * delete reaction
   * 
   * @param  int   $id
   * @return void
   * 
   * @since   1.0.0
   * @version 1.0.0
   * @author  Mahmudul Hasan Mithu
   */
  public static function delete(int $id): void{
    DB::table('RP_reaction')->where('id', $id)->delete();
  }


}
