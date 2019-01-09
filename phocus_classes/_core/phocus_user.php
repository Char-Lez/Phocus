<?php
  //
  // phocus_user.php
  //
  class phocus_user
  {
    /**
    * <h1>exists</h1>
    *
    * Use this to determine if a user ID exists
    * This doesn't load it or anything else, it only tells you if it exists in the database
    *
    * @param [string] $user_id
    *
    * @return [Boolean]
    */
    static public function exists($user_id)
    {
      try
      {
				//////////////////////////
				// Check argument count //
				//////////////////////////
				//
				$arg_count=func_num_args();
				confirm_args($arg_count, 1);
				//
				//
				///////////////////
				// CONFIRM TYPES //
				///////////////////
				//
				confirm_string($user_id);
				//
				//
				//////////////////////////////
				// DETERMINE IF USER EXISTS //
				//////////////////////////////
				//
        $SQL="SELECT count(*) FROM `account` WHERE uuid='#1#';";
        query($SQL, $user_id);
        //
        // Was exactly one ID found?
        if (row_count()===1)
        {
          // Yes, only one found
          $result=TRUE;
        }
        else
        {
          // No, not exactly one found
          $result=FALSE;
        } // if (row_count()===1)
        //
        return $result;
      }
      catch (Throwable $e)
      {
        throw new phocus_fault('Could not determine if user exists', '', $e);
      } // try
    } // exists()
  } // phocus_user
?>
