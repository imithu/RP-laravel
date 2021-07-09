<?php
namespace RP;

use Illuminate\Support\Facades\DB;


class Report
{


  /**
   * add or update report
   * 
   * @param string $base_key
   * @param int    $base_id
   * @param int    $id_user
   * @param string $value     // default is ''
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

    DB::table('RP_report')->updateOrInsert(
      [
        'base_key'=> $base_key,
        'base_id' => $base_id,
        'id_user' => $id_user,
        'value' => $value
      ],
      [
        'status' => 'publish',
        'datetime' => \Misc\Moment::datetime()
      ]
    );
  }


  /**
   * delete report
   * 
   * @param  int   $id
   * @return void
   * 
   * @since   1.0.0
   * @version 1.0.0
   * @author  Mahmudul Hasan Mithu
   */
  public static function delete(int $id): void{
    DB::table('RP_report')->where('id', $id)->delete();
  }


}
