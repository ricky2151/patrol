<?php
    function checkRangeTimeShift($timeNow, $dateNow, $dateYesterday, $dateShift, $startTimeShift, $endTimeShift, $typeCheck)
    {
        //typecheck can contains 3 value : 
        //between : return true if the current time is between shift times
        //greater : return true if the current time is now past shift time
        //smaller : return true if the current time hasn't passed shift time
        $result = false;

        //if normal
        //ex : 10:00 - 14:00
        if(strtotime($startTimeShift) < strtotime($endTimeShift))
        {
            //check if date is same
            if(strtotime($dateShift) == strtotime($dateNow))
            {
                if($typeCheck == "between") //check if timenow is in between starttimeshift and endtimeshift
                {
                    if(strtotime($timeNow) >= strtotime($startTimeShift) && strtotime($timeNow) < strtotime($endTimeShift))
                    {
                        $result = true;
                    }
                    else
                    {
                        return $result;
                    }
                } 
                else if($typeCheck == "greater") //check if timenow is greather than timeshift
                {
                    if(strtotime($timeNow) > strtotime($endTimeShift))
                    {
                        $result = true;
                    }
                    else
                    {
                        return $result;
                    }
                }
                else if($typeCheck == "smaller") //check if timenow is smaller than timeshift
                {
                    if(strtotime($timeNow) < strtotime($startTimeShift))
                    {
                        $result = true;
                    }
                    else
                    {
                        return $result;
                    }
                }
            }
            else
            {
                if($typeCheck == "between")
                {
                    return $result;
                }
                else if($typeCheck == "greater")
                {
                    if(strtotime($dateShift) < strtotime($dateNow))
                    {
                        //ex TimeShift : 09:00 - 10:00
                        //ex DateShift : 20-04-2020
                        //condition DateNow & TimeNow : 21-04-2020 08:00
                        $result = true;
                    }
                    else
                    {
                        return $result;
                    }
                }
                else if($typeCheck == "smaller")
                {
                    if(strtotime($dateShift) > strtotime($dateNow))
                    {
                        //ex TimeShift : 09:00 - 10:00
                        //ex DateShift : 22-04-2020
                        //condition DateNow & TimeNow : 21-04-2020 08:00
                        $result = true;
                    }
                    else
                    {
                        return $result;
                    }
                }
            }
        }
        else
        {
            //if overlap date
            //ex : 18:00 - 10:00
            //check if date is same
            if(strtotime($dateShift) == strtotime($dateNow))
            {
                if($typeCheck == "between") //scan time is must greather than startimteshift & endtimeshift. ex, scan time : 20:00
                {
                    if(strtotime($timeNow) > strtotime($startTimeShift) && strtotime($timeNow) > strtotime($endTimeShift))
                    {
                        $result = true;
                    }
                    else
                    {
                        return $result;
                    }
                }
                else if($typeCheck == "greater") //check if timenow is greather than timeshift
                {
                    //ex TimeShift : 18:00 - 10:00
                    //ex DateShift : 21-04-2020
                    //condition DateNow : 21-04-2020

                    //it is impossible for the current time to exceed the shift time
                    return $result;
                }
                else if($typeCheck == "smaller") //check if timenow is smaller than timeshift
                {
                    if(strtotime($timeNow) < strtotime($startTimeShift))
                    {
                        $result = true;
                    }
                    else
                    {
                        return $result;
                    }
                }
            }
            else if(strtotime($dateShift) == strtotime($dateYesterday)) //ex, shift date is 2020-04-19, and dateNow is 2020-04-20, then this is overlap date
            {
                //scan time is must less than starttimeshift & endtimeshift. ex, time shift : 18:00 - 10:00, scan time : 09:00
                if($typeCheck == "between")
                {
                    if(strtotime($timeNow) < strtotime($startTimeShift) && strtotime($timeNow) < strtotime($endTimeShift))
                    {
                        $result = true;
                    }
                    else
                    {
                        return $result;
                    }
                }
                else if($typeCheck == "greater") //check if timenow is greather than timeshift
                {
                    if(strtotime($timeNow) > strtotime($endTimeShift))
                    {
                        
                        $result = true;
                    }
                    else
                    {
                        return $result;
                    }
                }
                else if($typeCheck == "smaller") //check if timenow is smaller than timeshift
                {
                    //ex TimeShift : 18:00 - 10:00
                    //ex DateShift : 21-04-2020
                    //condition DateNow : 22-04-2020

                    //it is impossible for the current time to smaller the shift time
                    return $result;
                }

            }
            else
            {
                if($typeCheck == "between")
                {
                    return $result;
                }
                else if($typeCheck == "greater")
                {
                    if(strtotime($dateShift) < strtotime($dateNow))
                    {
                        //ex TimeShift : 09:00 - 10:00
                        //ex DateShift : 20-04-2020
                        //condition DateNow & TimeNow : 21-04-2020 08:00
                        $result = true;
                    }
                    else
                    {
                        return $result;
                    }
                }
                else if($typeCheck == "smaller")
                {
                    if(strtotime($dateShift) > strtotime($dateNow))
                    {
                        //ex TimeShift : 09:00 - 10:00
                        //ex DateShift : 22-04-2020
                        //condition DateNow & TimeNow : 21-04-2020 08:00
                        $result = true;
                    }
                    else
                    {
                        return $result;
                    }
                }
            }
        }
        
        return $result;
    }
?>