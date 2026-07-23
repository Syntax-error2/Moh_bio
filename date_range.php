<?php
$ctr=0;

$period=new DatePeriod(
            
            new DateTime('07/22/2019'),
            new DateInterval('P1D'),
            new DateTime('07/26/2019')
            
            );
            
            foreach($period as $key => $value){
                $ctr=$ctr+1;
                 echo $value->format('m/d/Y').'<br />';
            }
            
            
            echo $ctr;
?>