#!/bin/bash  
      
step=1  
      
for (( i = 0; i < 60; i=(i+step) )); do  
    $(php '/www/web/100gift/public_html/yii' 'init/gather2')
     sleep $step  
done  
      
exit 0  