<?php
    /**
    * @author Igor Shevchuk ishevchuk@zilorent.com
    */
  class text2timestamp{
      
      private $_timestamp;
      
      private $_action;
      
      private $_is_error=FALSE;
      
      private $_error_message;
      
      private $_days=array('sunday'=>0,'monday'=>1,'tuesday'=>2,'wednesday'=>3,'thursday'=>4,'friday'=>5,'saturday'=>6);
      
      function __construct($text_to_conver,$timezone=''){
          
          if ($timezone){
            date_default_timezone_set($timezone);    
          }
          
          
          $text_to_conver=preg_replace('/midnight/i','00:00:00am',$text_to_conver);
          $text_to_conver=preg_replace('/noon/i','12am',$text_to_conver);
          
          
          if(preg_match('/([0-9]{1,}\/[0-9]{1,}\/[0-9]{1,})[a-z\s]+([0-9:]{2,})(pm|am|p|a)([a-z0-9\D]+)/',$text_to_conver,$result)){
            $this->_set_action($result[4],'');
            $hours_need=(strtotime($result[1])-mktime(0,0,0))/60/60;
            if ($hours_need>0){
                $this->_set_timestamp($result[3],$result[2],$this->_get_special_minutes($result[2]),$hours_need,$hours_need);    
            }
            else{
                $this->_is_error=TRUE;
                $this->_error_message='Please choose date bigger than now';
                return ;
            }
          }
          elseif(preg_match('/(tomorrow|today|monday|tuesday|wednesday|thursday|friday|saturday|sunday)[a-z\s]+([0-9:0-9]+)\s{0,}((pm|am|p|a)|(hours|hrs|minutes|minute|mins|hour|hr|min|mn))/i',$text_to_conver,$result)){
            $this->_set_action($text_to_conver,$result[0]);
            $spesial_minutes=$this->_get_special_minutes($result[2]);            
            switch(strtolower($result[1])) {
                case 'today':{
                    $this->_set_timestamp($result[3],$result[2],$spesial_minutes);       
                    break;
                }
                case 'tomorrow':{
                    $this->_set_timestamp($result[3],$result[2],$spesial_minutes,24,24);       
                    break;
                }
                default:{
                    if ($this->_days[strtolower($result[1])]<date('w')){
                        //day passed, then need new
                        $this->_set_timestamp($result[3],$result[2],$spesial_minutes,
                            ($this->_days[strtolower($result[1])]+date('w')-3)*24,
                            ($this->_days[strtolower($result[1])]+date('w')-3)*24
                        );    
                    }
                    else{
                        //day will go
                        $this->_set_timestamp($result[3],$result[2],$spesial_minutes,
                            ($this->_days[strtolower($result[1])]-date('w'))*24,
                            ($this->_days[strtolower($result[1])]-date('w'))*24
                        );    
                    }
                    break;
                }
            }
          }
          //date with month
         elseif(preg_match('/(January|Jan|February|Feb|March|Mar|April|Apr|May|June|Jun|July|Jul|August|Aug|September|Sept|October|Oct|November|Nov|December|Dec)\s+([0-9]+)([a-z\s]+)([0-9:]{0,})(pm|am|p|a)([\s,]+)([a-z0-9\s-.]+)/i',$text_to_conver,$result)){
             if ($result[2]>=32){
                 $this->_is_error=TRUE;
                 $this->_error_message='Day can\'t be more then 31';
                 return ;
             }
            $this->_set_action($result[7],'');
            $result[5]=(($result[5]=='p') OR ($result[5]=='a'))?$result[5].'m':$result[5];
            if (date('m',strtotime($result[1]))<date('m')){
                //month passed, need new yaer
                 $this->_timestamp=strtotime("{$result[2]} {$result[1]}".(date('Y')+1)."{$result[4]}{$result[5]}");
            }
            else{
                //month will be, same year
                if (date('d',strtotime("{$result[2]} {$result[1]}"))<date('d')){
                    $next_month=date('F',strtotime('next month'));
                  //day paseed, need  new month  
                  $this->_timestamp=strtotime("{$result[2]} {$next_month}".date('Y')."{$result[4]}{$result[5]}");
                }
                else{
                    //day will be
                     $this->_timestamp=strtotime("{$result[2]} {$result[1]}".date('Y')."{$result[4]}{$result[5]}");
                }
            }
        }
          elseif(preg_match('/([0-9:0-9]+)\s{0,}((pm|am|p|a)|(hours|hrs|minutes|minute|mins|hour|hr|min|mn))/i',$text_to_conver,$result)){
              
            $this->_set_action($text_to_conver,$result[0]);
        
            //looking for hours|hour|hr|hrs
            if (preg_match('/[h]{1}[a-z]{0,2}[r]{1}/',$result[2])){
                $this->_timestamp=mktime(date('H')+$result[1],date('i'),0);
            }
            //looking for minutes|mins|min|mn|minute
            elseif(preg_match('/[m]{1}[a-z]{0,1}[n]{1}/',$result[2])){
                $this->_timestamp=mktime(date('H'),date('i')+$result[1],0);
            }
            else{
                $spesial_minutes=$this->_get_special_minutes($result[0]);
                $this->_set_timestamp($result[2],$result[1],$spesial_minutes);
            }
        }
        else{
            $this->_is_error=TRUE;
            $this->_error_message='I don\'t understand you. Try tell another time';
            return ;
        }
      }
      
      private function _set_timestamp($day_part,$time,$spesial_minutes,$hours_to_increase=24,$need_to_increase=0){
           $time=preg_replace('/:[0-9]+/','',$time);
          //check if use evening
            if (($day_part=='pm') OR ($day_part=='p')){
                if ($need_to_increase+date('H')>$time+12) {
                    //time passed, use tomorow
                    $this->_timestamp=mktime($hours_to_increase+$time+12,$spesial_minutes,0);
                }
                    else{
                    //use today 
                    $this->_timestamp=mktime($time+12,$spesial_minutes,0);
                }
            }
            //use day
            elseif(($day_part=='am') OR ($day_part=='a')){
                if ($need_to_increase+date('H')>$time){
                    //time passed, use tomorow
                    $this->_timestamp=mktime($hours_to_increase+$time,$spesial_minutes,0);
                }
                    else{
                    //use today 
                    $this->_timestamp=mktime($time,$spesial_minutes,0);
                }
            }
      }
      
      private function _get_special_minutes($time){
          if (strpos($time,':')){
            $time=preg_replace('/[a-z]{1,}/','',$time);
            $temp_minutes=explode(':',$time);
            return $temp_minutes[1];
          }
          return 0;
      }
      
      private function _set_action($action,$time){
          $this->_action=str_replace($time,'',$action);
      }
      
      function get_timestamp(){
          return $this->_timestamp;
      }
      
      function get_action(){
          return $this->_action;
      }
      
      function is_error_message(){
          return $this->_is_error;
      }
      
      function get_error_message(){
          return $this->_error_message;
      }
      
  }
?>