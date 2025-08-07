<?php

namespace App\Libraries;

class BusinessHoursLib 
{

    public function validate_start_time_end_time($start_time, $end_time)
    {
        if ($start_time < $end_time) {
            return true;
        }
        elseif ($start_time === $end_time && $start_time === '00:00:00' && $end_time === '00:00:00') {
            return true;
        }
        else {
            return false;
        }
    }

}
