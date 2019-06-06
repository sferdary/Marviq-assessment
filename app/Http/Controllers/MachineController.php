<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Production;
use App\Runtime;


/* ********************************************************************************************
/* This is the machine controller, it contains all the functions and calculations for
/* the machine dashboard.
/* Every function contains its own formulas for the specific task.
/* The results of the functions are stored in machine arrays found in the function index.
/* There are 2 database Models used in this controller:
/* Production -> Containing the variable $table specific for the database table 'Production'.
/* Runtime    -> Containing the variable $table specific for the database table 'Runtime'.
/* */
class MachineController extends Controller
{


    /* ********************************************************************************************
    /* The index function receives an request from the form of the dashboard page, that request is
    /* stored in the variable $datetime.
    /* This variable will be used in all the functions that are stored in the machine arrays.
    /* The machine arrays contain the specific data of that machine to provide the correct data.
    /* */
    public function index(Request $request)
    {
        if ($request->has('date') === true) {                                                   // If an request is made = true
            $datetime = date('Y-m-d H:i:s', strtotime($request->input("date") . ' 00:00:00'));  // The request will be converted to the correct format
        } else {
            $datetime = '2018-01-01 00:00:00';                                                  // This is the standard value if there is no request
        }

        $machine2x2 = array(
            "number"      => "01",
            "type"        => "2x2",
            "production"  => $prod = $this->production("2x2 brick mould", $datetime),
            "runtime"     => $runtime = $this->runtime("2x2 brick mould", $datetime),
            "temperature" => $this->temperature("2x2 brick mould", $datetime),
            "oee"         => $this->oee($prod['actualGross'], $prod['nettGross'], $prod['actualScrap'], $runtime['uptime'])
        );

        $machine3x2 = array(
            "number"      => "02",
            "type"        => "3x2",
            "production"  => $prod = $this->production("3x2 brick mould", $datetime),
            "runtime"     => $runtime = $this->runtime("3x2 brick mould", $datetime),
            "temperature" => $this->temperature("3x2 brick mould", $datetime),
            "oee"         => $this->oee($prod['actualGross'], $prod['nettGross'], $prod['actualScrap'], $runtime['uptime'])
        );

        $machine4x2 = array(
            "number"      => "03",
            "type"        => "4x2",
            "production"  => $prod = $this->production("4x2 brick mould", $datetime),
            "runtime"     => $runtime = $this->runtime("4x2 brick mould", $datetime),
            "temperature" => $this->temperature("4x2 brick mould", $datetime),
            "oee"         => $this->oee($prod['actualGross'], $prod['nettGross'], $prod['actualScrap'], $runtime['uptime'])
        );

        $result = array(                                        // The $result array contains the data of the machines
            "2x2"  => $machine2x2,
            "3x2"  => $machine3x2,
            "4x2"  => $machine4x2,
        );
        return view('pages.index')->with('result', $result);    // The $result array will be returned to the index view as the variable $result
    }


    /* ********************************************************************************************
    /* The function production contains the database queries from the table "Production" and
    /* formulas for the gross and scrap values.
    /* */
    protected function production($machineName, $datetime)
    {
        // Initializing local variables
        $start = strtotime($datetime);                          // $datetime will be converted into seconds from unix time
        $end   = $start + 3600;                                 // There are 3600 seconds (1 hour) add to the datetime, representing one hour later

        $gross = 0;
        $scrap = 0;
        $grossPerHour   = array();
        $scrapPerHour   = array();
        $nettPerHour = array();
        $chartData     = "";

        for ($i = 0; $i < 24; $i++) {                           // For loop to itterate through every hour in a day (24 times)
            $datetimeFrom = date('Y-m-d H:i:s', $start);        // The correct format for the datetime_from in the database based on start time
            $datetimeTo   = date('Y-m-d H:i:s', $end);          // The correct format for the datetime_to in the database based on end time
            if ($i < 9) {
                $time[$i] = "0" . ($i + 1) . ":00 ";            // Time on position $i with the value of $i+1 ($time[0]= 01:00)
            } else {
                $time[$i] = ($i + 1) . ":00";                   // Time on position $i with the value of $i+1 ($time[0]= 10:00)
            }
            // Database queries created with the query builder
            $gross = Production::select("value")                // Statement for collecting the gross production per hour
                ->where("machine_name",   "=", $machineName)
                ->where("variable_name",  "=", "PRODUCTION")
                ->where("datetime_from", ">=", $datetimeFrom)
                ->where("datetime_to",   "<=", $datetimeTo)
                ->sum("value");                                 // Sum of the gross in 1 hour (the data is given per 5 minutes)

            $scrap = Production::select("value")                // Statement for collecting the scrap per hour
                ->where("machine_name",   "=", $machineName)
                ->where("variable_name",  "=", "SCRAP")
                ->where("datetime_from", ">=", $datetimeFrom)
                ->where("datetime_to",   "<=", $datetimeTo)
                ->sum("value");                                 // Sum of the scrap in 1 hour (the data is given per 5 minutes)

            $grossPerHour[$i]   = $gross;                       // The sum of the hourly gross production will be stored in the array on position $i
            $scrapPerHour[$i]   = $scrap;                       // The sum of scrap per hour will be stored in the array on position $i
            $nettPerHour[$i]    = $gross - $scrap;              // Nett production per hour will be stored in the array on position $i

            // Chart data for the nett gross chart, concatinated to create JSON syntax
            $chartData .= "
            {
                hour:'" . $time[$i] . "',
                netto:" . $nettPerHour[$i] . "
            },";

            $start =  $end;                                     // The starttime becomes the endtime to fetch data from the database at one hour later
            $end   += 3600;                                     // The endtime will be incremented by 3600 seconds (1 hour)
        }

        if ($gross !== 0 && $scrap !== 0) {                     // Checking if there is data on the given datetime
            $result = array(                                    // If true, the results will be stored in the array $result (summed, stored or converted to an percentage)
                "actualGross" => array_sum($grossPerHour),
                "actualScrap" => array_sum($scrapPerHour),
                "nettGross"   => array_sum($nettPerHour),
                "nettPerHour" => $nettPerHour,
                "percScrap"   => round((array_sum($scrapPerHour) / array_sum($grossPerHour)) * 100, 1),
                "chartData"   => $chartData
            );
        } else {                                                // If false, the values will become 0 to avoid calculate errors in other functions
            $result = array(
                "actualGross" => 0,
                "actualScrap" => 0,
                "nettGross"   => 0,
                "nettPerHour" => 0,
                "percScrap"   => 0,
                "chartData"   => 0
            );
        }
        return $result;
    }


    /* ********************************************************************************************
    /* The function temperature contains the database queries from the table "Production" and
    /* formulas for the minimum, maximum and average temperature.
    /* */
    protected function temperature($machineName, $datetime)
    {
        // Initializing local variables
        $datetimeFrom = $datetime;                                  // The correct format for the datetime in the database incl the starttime
        $datetimeTo   = (new \DateTime($datetimeFrom))
            ->add(\DateInterval::createFromDateString('24 hours'));

        // Database queries created with the query builder
        $maxTemp = Production::select('value')                      // Statement for collecting max temperature on a day
            ->where('machine_name',   '=', $machineName)
            ->where('variable_name',  '=', "CORE TEMPERATURE")
            ->where('datetime_from', '>=', $datetimeFrom)
            ->where('datetime_to',   '<=', $datetimeTo)
            ->max('value');                                         // The maximum value of a day from the column where variable_name = CORE TEMPERATURE

        $minTemp = Production::select('value')                      // Statement for collecting min temperature on a day
            ->where('machine_name',   '=', $machineName)
            ->where('variable_name',  '=', "CORE TEMPERATURE")
            ->where('datetime_from', '>=', $datetimeFrom)
            ->where('datetime_to',   '<=', $datetimeTo)
            ->min('value');                                         // The minimum value of a day from the column where variable_name = CORE TEMPERATURE

        $avgTemp = Production::select('value')                      // Statement for collecting avg temperature on a day
            ->where('machine_name',   '=', $machineName)
            ->where('variable_name',  '=', "CORE TEMPERATURE")
            ->where('datetime_from', '>=', $datetimeFrom)
            ->where('datetime_to',   '<=', $datetimeTo)
            ->avg('value');                                         // The average value of a day from the column where variable_name = CORE TEMPERATURE

        return array(
            "maxTemp" => $maxTemp,
            "minTemp" => $minTemp,
            "avgTemp" => round($avgTemp, 1)                         // Value rounded by one decimal significant
        );
    }


    /* ********************************************************************************************
    /* The function runtime contains the database queries from the table "Runtime" and formulas
    /* for the up and down time.
    /* */
    protected function runtime($machineName, $datetime)
    {
        // Initializing local variables
        $datetimeFrom = $datetime;                                  // The datetime for datetime as start for the database query
        $datetimeTo   = (new \DateTime($datetimeFrom))              // The datetime for datetime as end for the database query (datetimeFrom incremented by 1 hour)
            ->add(\DateInterval::createFromDateString('24 hours'));

        $a = 0;                                                     // All even hours in one day
        $b = 0;                                                     // All odd hours in one day

        $uptime   = 0;
        $downtime = 0;
        $result   = 0;

        // Database queries created with the query builder
        $runtime = Runtime::select('*')                             // Statement for collecting an array of runtime information where the machine is running
            ->where('machine_name', '=', $machineName)
            ->where('datetime',    '>=', $datetimeFrom)
            ->where('datetime',    '<=', $datetimeTo)
            ->get('datetime');                                      // The array of datetimes will be fetched with this

        if (isset($runtime[0])) {                                   // Checking if there is an result on this query
            $index = count($runtime) - 1;                           // $index represents the length of the fetched $runtime array
            $i = 0;                                                 // $i represents the first position in the $runtime array
            $j = 1;                                                 // $J represents the second position in the $runtime array
            ($index % 2 === 0) ? $k = $j : $k = $i;                 // Checking if the length of the $runtime array is even or odd to exclude offsets in the for loop

            for ($i, $j, $k; $k <= $index; $i += 2, $j += 2, $k += 2) {                         // For loop to itterate through the $runtime array
                $a += strtotime(substr($runtime[$i]['datetime'], 11)) - strtotime("00:00:00");  // Sum of all the values on the even positions in the $runtime array
                $b += strtotime(substr($runtime[$j]['datetime'], 11)) - strtotime("00:00:00");  // Sum of all the values on the odd positions in the $runtime array
            }

            if ($runtime[0]['isrunning'] === 0 && $runtime[$index]['isrunning'] === 0) {        // Checking if the machine is OFF at the begin and end of the day
                $result = ($a - $b) / (60 * 60);                                                // Formula to calculate the runtime in hours
                $uptime = 24 + $result;                                                         // uptime in hours
                $downtime = round((-$result / 24) * 100, 1);                                    // downtime in percentage
            } else if ($runtime[0]['isrunning'] === 1 && $runtime[$index]['isrunning'] === 1) { // Checking if the machine is ON at the begin and end of the day
                $result = ($a - $b) / (60 * 60);                                                // Formula to calculate the runtime in hours
                $uptime = -$result;                                                             // uptime in hours
                $downtime = 100 - round((-$result / 24) * 100, 1);                              // downtime in percentage
            } else if ($runtime[0]['isrunning'] === 1 && $runtime[$index]['isrunning'] === 0) { // Checking if the machine is ON at the begin and OFF at the end of the day
                $result = ($b - $a) / (60 * 60);                                                // Formula to calculate the runtime in hours
                $uptime = 24 + $result;                                                         // uptime in hours
                $downtime = round((-$result / 24) * 100, 1);                                    // downtime in percentage
            } else if ($runtime[0]['isrunning'] === 0 && $runtime[$index]['isrunning'] === 1) { // Checking if the machine is OFF at the begin and ON at the end of the day
                $result = ($b - $a) / (60 * 60);                                                // Formula to calculate the runtime in hours
                if ($result < 0) {                                                              // Checking if $result is an negative value
                    $result = -$result;                                                         // Inverting $result from negative to positive
                    $uptime = $result;                                                          // uptime in hours
                    $downtime = 100 - round(($result / 24) * 100, 1);                           // downtime in percentage
                } else {                                                                        // If $result is positive
                    $uptime = 24 - $result;                                                     // uptime in hours
                    $downtime = round(($result / 24) * 100, 1);                                 // downtime in percentage
                }
            }
        }
        return array(
            "uptime"   => $uptime,
            "downtime" => $downtime
        );
    }


    /* ********************************************************************************************
    /* The function oee contains formulas for the performance, availebility and quality in
    /* percentages.
    /* This three percentages are used to calculate the oee (overall equipment efficiency).
    /* */
    protected function oee($totGross, $nettGross, $totScrap, $uptime)
    {
        if ($totGross !== 0 && $nettGross !== 0 && $totScrap !== 0 && $uptime !== 0) {      // Checking if there are any results
            $performance  = ($totGross / 720000) * 100;                                     // Calculating perfomance of the machine (720000 = 30.000 bricks p/hour * 24 hours)
            $availability = ($uptime / 18) * 100;                                           // Calculating availebility of the machine (18 = 75% uptime of 24 hours)
            $quality      = ($nettGross / $totGross) * 100;                                 // Calculating quality of the production
        } else {                                                                            // If no result, variables will become 0 to prevent calculation errors
            $performance  = 0;
            $availability = 0;
            $quality      = 0;
        }
        return round((($performance + $availability + $quality) / 3), 1);                   // OEE is rounded to one decimal significant
    }
}
